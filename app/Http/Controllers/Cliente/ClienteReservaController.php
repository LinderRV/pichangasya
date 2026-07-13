<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\Cancha;
use App\Models\ComplejoDeportivo;
use App\Models\MetodoPago;
use App\Models\Reserva;
use App\Models\Pago;
use App\Models\HistorialEstadoReserva;
use App\Models\EstadoReserva;
use App\Services\DisponibilidadService;
use App\Services\NiubizService;
use App\Mail\ReservaConfirmadaMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ClienteReservaController extends Controller
{
    public function __construct(
        private DisponibilidadService $disponibilidad,
        private NiubizService $niubiz
    ) {}

    // ─── NIUBIZ: Crear sesión de pago ─────────────────────────────────────────

    public function niubizSesion(Request $request)
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

            $purchaseNumber = NiubizService::generarPurchaseNumber();

            $simulado   = (bool) config('niubiz.simulado');
            $sessionKey = $simulado ? null : $this->niubiz->crearSesion(
                $total,
                $request->ip(),
                $usuario->email
            );

            // Guardar intención de reserva en sesión (expira en 15 min)
            session([
                'niubiz_pending' => [
                    'id_cancha'      => $cancha->id,
                    'id_cliente'     => $cliente->id,
                    'id_usuario'     => $usuario->id,
                    'email'          => $usuario->email,
                    'fecha'          => $request->fecha,
                    'hora_inicio'    => $request->hora_inicio,
                    'hora_fin'       => $request->hora_fin,
                    'precio_hora'    => $cancha->precio_hora,
                    'total'          => $total,
                    'purchase_number'=> $purchaseNumber,
                    'expires_at'     => now()->addMinutes(15)->timestamp,
                ],
            ]);

            return response()->json(Service::responseSuccess('OK', [
                'session_key'     => $sessionKey,
                'purchase_number' => $purchaseNumber,
                'amount'          => number_format($total, 2, '.', ''),
                'merchant_id'     => config('niubiz.merchant_id'),
                'action_url'      => route('cliente.niubiz.confirmar'),
                'simulado'        => $simulado,
            ]));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(Service::responseError($e->validator->errors()->first()));
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al conectar con la pasarela de pago: ' . $e->getMessage()));
        }
    }

    // ─── NIUBIZ: Confirmar pago (action URL llamada por el navegador) ─────────

    public function niubizConfirmar(Request $request)
    {
        if (config('niubiz.simulado')) {
            return $this->niubizConfirmarSimulado($request);
        }

        $transactionToken = $request->input('transactionToken');

        if (!$transactionToken) {
            return redirect()->route('web.paginas.canchas')
                ->with('error', 'No se recibió confirmación del pago.');
        }

        $pending = session('niubiz_pending');

        if (!$pending) {
            return redirect()->route('web.paginas.canchas')
                ->with('error', 'Sesión de pago expirada. Intenta de nuevo.');
        }

        if (now()->timestamp > $pending['expires_at']) {
            session()->forget('niubiz_pending');
            return redirect()->route('web.paginas.canchas')
                ->with('error', 'La sesión de pago expiró. Intenta de nuevo.');
        }

        try {
            // Autorizar con Niubiz
            $resultado = $this->niubiz->autorizar(
                $transactionToken,
                $pending['purchase_number'],
                $pending['total'],
                $request->ip(),
                $pending['email']
            );

            $codigoAutorizacion = $resultado['order']['authorizationCode']
                ?? $resultado['dataMap']['AUTHORIZATION_CODE']
                ?? null;

            if (!$codigoAutorizacion) {
                $errorMsg = $resultado['order']['actionDescriptionCode']
                    ?? $resultado['dataMap']['ACTION_DESCRIPTION']
                    ?? 'Pago rechazado.';
                return redirect()
                    ->route('web.paginas.cancha', $pending['id_cancha'])
                    ->with('error', 'Pago rechazado: ' . $errorMsg);
            }

            $reserva = $this->completarReserva($pending, $codigoAutorizacion);

            if (!$reserva) {
                return redirect()
                    ->route('web.paginas.cancha', $pending['id_cancha'])
                    ->with('error', 'El horario fue ocupado mientras realizabas el pago. Contáctanos para el reembolso.');
            }

            session()->forget('niubiz_pending');

            return redirect()->route('cliente.reservas')
                ->with('success', '¡Reserva confirmada! Tu pago fue procesado exitosamente.');
        } catch (Exception $e) {
            return redirect()
                ->route('web.paginas.cancha', $pending['id_cancha'] ?? 0)
                ->with('error', 'Error al confirmar la reserva: ' . $e->getMessage());
        }
    }

    // ─── NIUBIZ: Confirmar pago SIMULADO (sin llamar a la API real) ───────────

    private function niubizConfirmarSimulado(Request $request)
    {
        $pending = session('niubiz_pending');

        if (!$pending || now()->timestamp > $pending['expires_at']) {
            session()->forget('niubiz_pending');
            return response()->json(Service::responseError('La sesión de pago expiró. Intenta de nuevo.'));
        }

        $numeroTarjeta = preg_replace('/\D+/', '', (string) $request->input('numero_tarjeta'));
        $nombreTitular = trim((string) $request->input('nombre_titular'));
        $vencimiento   = trim((string) $request->input('vencimiento'));
        $cvv           = trim((string) $request->input('cvv'));

        if ($nombreTitular === '') {
            return response()->json(Service::responseError('Ingresa el nombre del titular.'));
        }

        $tarjetasPrueba = ['5031755734530604', '4009175332806176'];

        if (!in_array($numeroTarjeta, $tarjetasPrueba, true) || $vencimiento !== '11/30' || $cvv !== '123') {
            return response()->json(Service::responseError('Tarjeta rechazada. Verifica los datos e intenta de nuevo.'));
        }

        try {
            $reserva = $this->completarReserva($pending, 'SIM-' . strtoupper(Str::random(8)));
        } catch (Exception $e) {
            return response()->json(Service::responseError('No se pudo confirmar la reserva. Intenta de nuevo.'));
        }

        if (!$reserva) {
            return response()->json(Service::responseError('El horario fue ocupado mientras realizabas el pago.'));
        }

        session()->forget('niubiz_pending');

        return response()->json(Service::responseSuccess('OK', [
            'redirect' => route('cliente.reservas'),
        ]));
    }

    // ─── Crea Reserva + Pago + Historial dentro de una transacción bloqueada ──

    private function completarReserva(array $pending, string $codigoAutorizacion): ?Reserva
    {
        $cancha = Cancha::findOrFail($pending['id_cancha']);
        $duracionMinutos = (int) ((strtotime($pending['hora_fin']) - strtotime($pending['hora_inicio'])) / 60);

        $metodoPago = MetodoPago::firstOrCreate(
            ['nombre' => 'Niubiz'],
            ['estado' => 'activo']
        );

        // Bloquea la cancha para que dos pagos concurrentes por el mismo
        // horario no pasen ambos la verificación de disponibilidad.
        $reserva = DB::transaction(function () use ($pending, $cancha, $duracionMinutos, $codigoAutorizacion, $metodoPago) {
            Cancha::where('id', $cancha->id)->lockForUpdate()->first();

            $slots = $this->disponibilidad->slotsDisponibles($cancha, $pending['fecha'], $duracionMinutos);
            $slotValido = collect($slots)->first(fn($s) =>
                $s['hora_inicio'] === $pending['hora_inicio'] &&
                $s['hora_fin']    === $pending['hora_fin']
            );

            if (!$slotValido) {
                return null;
            }

            $ahora = now();

            $reserva = Reserva::create([
                'codigo_reserva'    => $this->generarCodigo(),
                'id_cliente'        => $pending['id_cliente'],
                'id_cancha'         => $cancha->id,
                'id_estado_reserva' => EstadoReserva::CONFIRMADA,
                'fecha_reserva'     => $pending['fecha'],
                'hora_inicio'       => $pending['hora_inicio'],
                'hora_fin'          => $pending['hora_fin'],
                'precio_hora'       => $pending['precio_hora'],
                'subtotal'          => $pending['total'],
                'total'             => $pending['total'],
                'confirmado_at'     => $ahora,
            ]);

            Pago::create([
                'id_reserva'       => $reserva->id,
                'id_metodo_pago'   => $metodoPago->id,
                'codigo_operacion' => $pending['purchase_number'] . '-' . $codigoAutorizacion,
                'monto'            => $pending['total'],
                'comprobante_url'  => null,
                'estado'           => 'confirmado',
                'fecha_pago'       => $ahora,
            ]);

            HistorialEstadoReserva::create([
                'id_reserva'        => $reserva->id,
                'id_estado_reserva' => EstadoReserva::CONFIRMADA,
                'id_usuario'        => $pending['id_usuario'],
                'fecha_cambio'      => $ahora,
                'observacion'       => 'Reserva confirmada vía Niubiz. Auth: ' . $codigoAutorizacion,
            ]);

            return $reserva;
        });

        if ($reserva) {
            $this->enviarCorreoConfirmacion($reserva);
        }

        return $reserva;
    }

    private function enviarCorreoConfirmacion(Reserva $reserva): void
    {
        try {
            $reserva->load(['cliente.usuario', 'cancha.complejo']);
            $email = optional($reserva->cliente->usuario)->email;
            if ($email) {
                Mail::to($email)->send(new ReservaConfirmadaMail($reserva));
            }
        } catch (Exception $e) {
            Log::error('No se pudo enviar correo de confirmación de reserva: ' . $e->getMessage());
        }
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

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function generarCodigo(): string
    {
        do {
            $codigo = 'PY-' . date('ym') . strtoupper(Str::random(5));
        } while (Reserva::where('codigo_reserva', $codigo)->exists());
        return $codigo;
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
