<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\Cancha;
use App\Models\ComplejoDeportivo;
use App\Models\Reserva;
use App\Services\DisponibilidadService;
use App\Services\StripeService;
use App\Services\ReservaPagoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ClienteReservaController extends Controller
{
    public function __construct(
        private DisponibilidadService $disponibilidad,
        private StripeService $stripe,
        private ReservaPagoService $reservaPago
    ) {}

    // ─── STRIPE: Crear Checkout Session ───────────────────────────────────────

    public function stripeSesion(Request $request)
    {
        try {
            $request->validate([
                'id_cancha'   => ['required', 'exists:canchas,id'],
                'fecha'       => ['required', 'date', 'after_or_equal:today'],
                'hora_inicio' => ['required', 'date_format:H:i'],
                'hora_fin'    => ['required', 'date_format:H:i', 'after:hora_inicio'],
            ]);

            $usuario = Auth::user();
            $cliente = $usuario->cliente;

            if (!$cliente) {
                return response()->json(Service::responseError('Completa tu perfil antes de reservar.'));
            }

            $cancha = Cancha::findOrFail($request->id_cancha);

            $duracionMinutos = (int) ((strtotime($request->hora_fin) - strtotime($request->hora_inicio)) / 60);
            if (!DisponibilidadService::duracionValida($duracionMinutos)) {
                return response()->json(Service::responseError('Duración de reserva no válida.'));
            }

            // Verificar disponibilidad en tiempo real
            $slots = $this->disponibilidad->slotsDisponibles($cancha, $request->fecha, $duracionMinutos);
            $slotValido = collect($slots)->first(fn($s) =>
                $s['hora_inicio'] === $request->hora_inicio &&
                $s['hora_fin']    === $request->hora_fin
            );

            if (!$slotValido) {
                return response()->json(Service::responseError('El horario ya no está disponible. Por favor elige otro.'));
            }

            $total = round($cancha->precio_hora * ($duracionMinutos / 60), 2);

            $pending = [
                'id_cancha'       => $cancha->id,
                'id_cliente'      => $cliente->id,
                'id_usuario'      => $usuario->id,
                'fecha'           => $request->fecha,
                'hora_inicio'     => $request->hora_inicio,
                'hora_fin'        => $request->hora_fin,
                'precio_hora'     => $cancha->precio_hora,
                'total'           => $total,
                'purchase_number' => StripeService::generarPurchaseNumber(),
            ];

            $session = $this->stripe->crearCheckoutSession(
                $pending,
                route('cliente.stripe.success'),
                route('web.paginas.cancha', $cancha->id),
                $usuario->email
            );

            return response()->json(Service::responseSuccess('OK', [
                'url' => $session->url,
            ]));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(Service::responseError($e->validator->errors()->first()));
        } catch (Exception $e) {
            Log::error('Error al crear Checkout Session de Stripe: ' . $e->getMessage());
            return response()->json(Service::responseError('Error al conectar con la pasarela de pago.'));
        }
    }

    // ─── STRIPE: success_url — respaldo síncrono si el webhook aún no llegó ──

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('web.paginas.canchas')
                ->with('error', 'No se recibió confirmación del pago.');
        }

        try {
            $session = $this->stripe->obtenerSesion($sessionId);
        } catch (Exception $e) {
            return redirect()->route('web.paginas.canchas')
                ->with('error', 'No se pudo verificar el pago. Contáctanos si el cargo fue realizado.');
        }

        if ($session->payment_status !== 'paid') {
            return redirect()->route('web.paginas.canchas')
                ->with('error', 'El pago no fue completado.');
        }

        $reserva = $this->reservaPago->confirmarPagoStripe(
            $session->metadata->toArray(),
            (string) $session->payment_intent
        );

        if (!$reserva) {
            return redirect()->route('web.paginas.canchas')
                ->with('error', 'El horario fue ocupado mientras realizabas el pago. Contáctanos para el reembolso.');
        }

        return redirect()->route('cliente.reservas')
            ->with('success', '¡Reserva confirmada! Tu pago fue procesado exitosamente.');
    }

    // ─── LISTA DE RESERVAS DEL CLIENTE ────────────────────────────────────────

    public function lista()
    {
        try {
            $cliente = Auth::user()->cliente;
            if (!$cliente) {
                return response()->json(Service::responseSuccess('OK', []));
            }

            $reservas = Reserva::with([
                'cancha.complejo:id,nombre,telefono',
                'estadoReserva:id,nombre',
                'pago.metodoPago:id,nombre',
            ])
            ->where('id_cliente', $cliente->id)
            ->orderByDesc('fecha_reserva')
            ->get()
            ->map(fn($r) => [
                'id'                => $r->id,
                'codigo'            => $r->codigo_reserva,
                'cancha'            => optional($r->cancha)->nombre ?? '-',
                'complejo'          => optional(optional($r->cancha)->complejo)->nombre ?? '-',
                'telefono_complejo' => optional(optional($r->cancha)->complejo)->telefono ?? '',
                'fecha'             => $r->fecha_reserva,
                'hora_inicio'       => substr($r->hora_inicio, 0, 5),
                'hora_fin'          => substr($r->hora_fin, 0, 5),
                'total'             => 'S/ ' . number_format($r->total, 2),
                'metodo_pago'       => optional(optional($r->pago)->metodoPago)->nombre ?? '-',
                'estado'            => optional($r->estadoReserva)->nombre ?? '-',
                'tiene_pago'        => (bool) $r->pago,
            ]);

            return response()->json(Service::responseSuccess('OK', $reservas));
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener reservas.'));
        }
    }

    public function detalle($id)
    {
        $cliente = Auth::user()->cliente;

        if (!$cliente) {
            abort(404);
        }

        $reserva = Reserva::with([
            'cancha.complejo:id,nombre,direccion,telefono,correo',
            'estadoReserva:id,nombre',
            'pago.metodoPago:id,nombre',
            'historial.estadoReserva:id,nombre',
            'reembolso',
        ])
            ->where('id_cliente', $cliente->id)
            ->findOrFail($id);

        return response()->json(Service::responseSuccess('OK', [
            'codigo'              => $reserva->codigo_reserva,
            'cancha'              => optional($reserva->cancha)->nombre ?? '-',
            'complejo'            => optional(optional($reserva->cancha)->complejo)->nombre ?? '-',
            'direccion'           => optional(optional($reserva->cancha)->complejo)->direccion ?? '-',
            'telefono_complejo'   => optional(optional($reserva->cancha)->complejo)->telefono,
            'correo_complejo'     => optional(optional($reserva->cancha)->complejo)->correo,
            'fecha'               => date('d/m/Y', strtotime($reserva->fecha_reserva)),
            'horario'             => substr($reserva->hora_inicio, 0, 5) . ' – ' . substr($reserva->hora_fin, 0, 5),
            'total'               => 'S/ ' . number_format($reserva->total, 2),
            'estado'              => optional($reserva->estadoReserva)->nombre ?? '-',
            'motivo_cancelacion'  => $reserva->motivo_cancelacion,
            'pago'                => $reserva->pago ? [
                'metodo' => optional($reserva->pago->metodoPago)->nombre ?? '-',
                'estado' => ucfirst($reserva->pago->estado),
                'fecha'  => optional($reserva->pago->fecha_pago)?->format('d/m/Y H:i'),
                'monto'  => 'S/ ' . number_format($reserva->pago->monto, 2),
            ] : null,
            'reembolso'           => $reserva->reembolso ? [
                'metodo' => ucfirst($reserva->reembolso->metodo_reembolso),
                'monto'  => 'S/ ' . number_format($reserva->reembolso->monto, 2),
                'fecha'  => optional($reserva->reembolso->fecha_reembolso)?->format('d/m/Y H:i'),
                'codigo' => $reserva->reembolso->codigo_operacion,
            ] : null,
            'historial'           => $reserva->historial->map(fn($item) => [
                'estado' => optional($item->estadoReserva)->nombre ?? '-',
                'fecha'  => optional($item->fecha_cambio)?->format('d/m/Y H:i'),
            ])->values(),
        ]));
    }

    // ─── Comprobante de pago (PDF) ────────────────────────────────────────────

    public function comprobantePdf($idReserva)
    {
        $cliente = Auth::user()->cliente;

        $reserva = Reserva::with([
            'cliente.usuario',
            'cancha.complejo',
            'pago.metodoPago',
        ])->findOrFail($idReserva);

        if (!$cliente || $reserva->id_cliente !== $cliente->id) {
            abort(403);
        }

        if (!$reserva->pago) {
            abort(404);
        }

        $usuario = optional($reserva->cliente)->usuario;

        $pdf = Pdf::loadView('pdf.comprobante_pago', [
            'pago'             => $reserva->pago,
            'reserva'          => $reserva,
            'complejo'         => optional($reserva->cancha)->complejo,
            'clienteNombre'    => trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellidos ?? '')) ?: '-',
            'clienteDocumento' => optional($reserva->cliente)->documento_identidad,
            'clienteEmail'     => $usuario->email ?? null,
        ])->setPaper('a4');

        return $pdf->stream('comprobante-' . $reserva->pago->codigo_operacion . '.pdf');
    }

    // ─── Legacy - mantener para no romper rutas existentes ───────────────────

    public function reservar()
    {
        return redirect()->route('web.paginas.canchas');
    }

    public function canchasPorComplejo($idComplejo)
    {
        try {
            $canchas = Cancha::where('id_complejo', $idComplejo)
                ->where('estado', 'activo')
                ->select('id', 'nombre', 'precio_hora')
                ->orderBy('nombre')
                ->get();
            return response()->json(Service::responseSuccess('OK', $canchas));
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener canchas.'));
        }
    }

    public function slots($idCancha, $fecha)
    {
        try {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || $fecha < date('Y-m-d')) {
                return response()->json(Service::responseError('Fecha inválida.'));
            }
            $cancha = Cancha::find($idCancha);
            if (!$cancha || $cancha->estado !== 'activo') {
                return response()->json(Service::responseError('Cancha no disponible.'));
            }
            $slots = $this->disponibilidad->slotsDisponibles($cancha, $fecha);
            return response()->json(Service::responseSuccess('OK', $slots));
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener disponibilidad.'));
        }
    }
}
