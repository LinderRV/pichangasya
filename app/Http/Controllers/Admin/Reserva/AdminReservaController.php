<?php

namespace App\Http\Controllers\Admin\Reserva;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\Reserva;
use App\Models\Pago;
use App\Models\Reembolso;
use App\Models\HistorialEstadoReserva;
use App\Models\EstadoReserva;
use App\Models\UsuarioComplejo;
use App\Models\Cancha;
use App\Services\DisponibilidadService;
use App\Mail\ReservaCanceladaMail;
use App\Mail\ReservaReprogramadaMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Exception;

class AdminReservaController extends Controller
{
    public function index()
    {
        return view('admin.reserva.index', [
            'duraciones' => DisponibilidadService::DURACIONES_PERMITIDAS,
        ]);
    }

    public function lista()
    {
        try {
            $query = Reserva::with([
                'cancha.complejo:id,nombre,telefono',
                'cliente.usuario:id,nombres,apellidos',
                'estadoReserva:id,nombre',
                'pago.metodoPago:id,nombre',
            ]);

            if (!Auth::user()->esSuperAdmin()) {
                $idComplejo = UsuarioComplejo::where('id_usuario', Auth::id())->value('id_complejo');
                $query->whereHas('cancha', fn($q) => $q->where('id_complejo', $idComplejo));
            }

            $reservas = $query->orderByDesc('fecha_reserva')->orderByDesc('hora_inicio')->get()->map(fn($r) => [
                'id'           => $r->id,
                'codigo'       => $r->codigo_reserva,
                'cliente'      => optional(optional($r->cliente)->usuario)->nombres . ' ' . optional(optional($r->cliente)->usuario)->apellidos,
                'cancha'       => optional($r->cancha)->nombre ?? '-',
                'complejo'     => optional(optional($r->cancha)->complejo)->nombre ?? '-',
                'fecha'        => $r->fecha_reserva,
                'hora_inicio'  => substr($r->hora_inicio, 0, 5),
                'hora_fin'     => substr($r->hora_fin, 0, 5),
                'total'        => number_format($r->total, 2),
                'metodo_pago'  => optional(optional($r->pago)->metodoPago)->nombre ?? '-',
                'estado'       => optional($r->estadoReserva)->nombre ?? '-',
                'telefono_complejo' => optional(optional($r->cancha)->complejo)->telefono ?? '',
            ]);

            return response()->json(Service::responseSuccess('OK', $reservas));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener reservas.'));
        }
    }

    public function obtener($id)
    {
        try {
            $reserva = Reserva::with([
                'cancha.complejo',
                'cliente.usuario',
                'estadoReserva',
                'pago.metodoPago',
            ])->find($id);

            if (!$reserva) {
                return response()->json(Service::responseError('Reserva no encontrada.'), 404);
            }

            $this->verificarAcceso($reserva);

            return response()->json(Service::responseSuccess('OK', [
                'id'               => $reserva->id,
                'codigo'           => $reserva->codigo_reserva,
                'cliente'          => optional(optional($reserva->cliente)->usuario)->nombres . ' ' . optional(optional($reserva->cliente)->usuario)->apellidos,
                'email'            => optional(optional($reserva->cliente)->usuario)->email,
                'cancha'           => optional($reserva->cancha)->nombre,
                'complejo'         => optional(optional($reserva->cancha)->complejo)->nombre,
                'telefono_complejo'=> optional(optional($reserva->cancha)->complejo)->telefono,
                'fecha'            => $reserva->fecha_reserva,
                'hora_inicio'      => substr($reserva->hora_inicio, 0, 5),
                'hora_fin'         => substr($reserva->hora_fin, 0, 5),
                'precio_hora'      => $reserva->precio_hora,
                'total'            => $reserva->total,
                'metodo_pago'      => optional(optional($reserva->pago)->metodoPago)->nombre,
                'codigo_operacion' => optional($reserva->pago)->codigo_operacion,
                'comprobante_url'  => optional($reserva->pago)->comprobante_url,
                'estado'           => optional($reserva->estadoReserva)->nombre,
                'confirmado_at'    => $reserva->confirmado_at?->format('d/m/Y H:i'),
                'cancelado_at'     => $reserva->cancelado_at?->format('d/m/Y H:i'),
                'motivo_cancelacion' => $reserva->motivo_cancelacion,
            ]));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener la reserva.'));
        }
    }

    public function cancelar(Request $request, $id)
    {
        try {
            $reserva = Reserva::with('pago', 'cliente.usuario', 'cancha.complejo')->find($id);
            if (!$reserva) {
                return response()->json(Service::responseError('Reserva no encontrada.'), 404);
            }

            $this->verificarAcceso($reserva);

            if ($reserva->id_estado_reserva === EstadoReserva::CANCELADA) {
                return response()->json(Service::responseError('La reserva ya está cancelada.'));
            }

            $request->validate([
                'motivo_cancelacion'  => ['required', 'string', 'max:255'],
                'metodo_reembolso'    => ['required', 'in:yape,plin,transferencia,efectivo,otro'],
                'monto_reembolso'     => ['required', 'numeric', 'min:0'],
                'codigo_reembolso'    => ['nullable', 'string', 'max:100'],
                'observacion_reembolso' => ['nullable', 'string', 'max:255'],
            ]);

            DB::transaction(function () use ($request, $reserva) {
                $ahora = now();

                $reserva->update([
                    'id_estado_reserva'   => EstadoReserva::CANCELADA,
                    'cancelado_at'        => $ahora,
                    'id_usuario_cancelado'=> Auth::id(),
                    'motivo_cancelacion'  => $request->motivo_cancelacion,
                ]);

                if ($reserva->pago) {
                    $reserva->pago->update(['estado' => 'reembolsado']);

                    Reembolso::create([
                        'id_reserva'       => $reserva->id,
                        'id_pago'          => $reserva->pago->id,
                        'id_usuario'       => Auth::id(),
                        'monto'            => $request->monto_reembolso,
                        'metodo_reembolso' => $request->metodo_reembolso,
                        'codigo_operacion' => $request->codigo_reembolso,
                        'observacion'      => $request->observacion_reembolso,
                        'fecha_reembolso'  => $ahora,
                    ]);
                }

                HistorialEstadoReserva::create([
                    'id_reserva'       => $reserva->id,
                    'id_estado_reserva'=> EstadoReserva::CANCELADA,
                    'id_usuario'       => Auth::id(),
                    'fecha_cambio'     => $ahora,
                    'observacion'      => $request->motivo_cancelacion,
                ]);
            });

            $this->enviarCorreoCancelacion($reserva, $request->metodo_reembolso, (float) $request->monto_reembolso);

            return response()->json(Service::responseSuccess('Reserva cancelada y reembolso registrado.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al cancelar la reserva.'));
        }
    }

    private function enviarCorreoCancelacion(Reserva $reserva, string $metodoReembolso, float $montoReembolso): void
    {
        try {
            $email = optional($reserva->cliente->usuario)->email;
            if ($email) {
                Mail::to($email)->send(new ReservaCanceladaMail($reserva, $metodoReembolso, $montoReembolso));
            }
        } catch (Exception $e) {
            Log::error('No se pudo enviar correo de cancelación de reserva: ' . $e->getMessage());
        }
    }

    public function slotsReprogramar(Request $request, $id, DisponibilidadService $disponibilidad)
    {
        try {
            $reserva = Reserva::with('cancha')->find($id);
            if (!$reserva) {
                return response()->json(Service::responseError('Reserva no encontrada.'), 404);
            }

            $this->verificarAcceso($reserva);

            if ($reserva->id_estado_reserva !== EstadoReserva::CONFIRMADA) {
                return response()->json(Service::responseError('Solo se pueden reprogramar reservas confirmadas.'));
            }

            $request->validate([
                'fecha'    => ['required', 'date', 'after_or_equal:today'],
                'duracion' => ['required', 'integer'],
            ]);

            if (!DisponibilidadService::duracionValida((int) $request->duracion)) {
                return response()->json(Service::responseError('Duración no válida.'));
            }

            $slots = $disponibilidad->slotsDisponibles(
                $reserva->cancha,
                $request->fecha,
                (int) $request->duracion,
                $reserva->id
            );

            return response()->json(Service::responseSuccess('OK', $slots));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener horarios disponibles.'));
        }
    }

    public function reprogramar(Request $request, $id, DisponibilidadService $disponibilidad)
    {
        try {
            $reserva = Reserva::with('cancha.complejo', 'cliente.usuario')->find($id);
            if (!$reserva) {
                return response()->json(Service::responseError('Reserva no encontrada.'), 404);
            }

            $this->verificarAcceso($reserva);

            if ($reserva->id_estado_reserva !== EstadoReserva::CONFIRMADA) {
                return response()->json(Service::responseError('Solo se pueden reprogramar reservas confirmadas.'));
            }

            $request->validate([
                'fecha'                  => ['required', 'date', 'after_or_equal:today'],
                'hora_inicio'            => ['required', 'date_format:H:i'],
                'hora_fin'               => ['required', 'date_format:H:i', 'after:hora_inicio'],
                'motivo_reprogramacion'  => ['required', 'string', 'max:255'],
            ]);

            $duracionMinutos = (strtotime($request->hora_fin) - strtotime($request->hora_inicio)) / 60;

            if (!DisponibilidadService::duracionValida((int) $duracionMinutos)) {
                return response()->json(Service::responseError('El horario seleccionado no tiene una duración válida.'));
            }

            $fechaAnterior = $reserva->fecha_reserva;
            $horaInicioAnterior = substr($reserva->hora_inicio, 0, 5);
            $horaFinAnterior = substr($reserva->hora_fin, 0, 5);

            DB::transaction(function () use ($request, $reserva, $disponibilidad, $duracionMinutos, $fechaAnterior, $horaInicioAnterior, $horaFinAnterior) {
                $cancha = Cancha::where('id', $reserva->id_cancha)->lockForUpdate()->first();

                $slots = $disponibilidad->slotsDisponibles($cancha, $request->fecha, (int) $duracionMinutos, $reserva->id);
                $disponible = collect($slots)->contains(fn($s) => $s['hora_inicio'] === $request->hora_inicio . ':00' || $s['hora_inicio'] === $request->hora_inicio);

                if (!$disponible) {
                    throw new Exception('El horario seleccionado ya no está disponible.');
                }

                $reserva->update([
                    'fecha_reserva' => $request->fecha,
                    'hora_inicio'   => $request->hora_inicio,
                    'hora_fin'      => $request->hora_fin,
                ]);

                HistorialEstadoReserva::create([
                    'id_reserva'        => $reserva->id,
                    'id_estado_reserva' => EstadoReserva::CONFIRMADA,
                    'id_usuario'        => Auth::id(),
                    'fecha_cambio'      => now(),
                    'observacion'       => "Reprogramada de {$fechaAnterior} {$horaInicioAnterior}-{$horaFinAnterior} a {$request->fecha} {$request->hora_inicio}-{$request->hora_fin}. Motivo: {$request->motivo_reprogramacion}",
                ]);
            });

            $this->enviarCorreoReprogramacion($reserva, $fechaAnterior, $horaInicioAnterior, $horaFinAnterior, $request->motivo_reprogramacion);

            return response()->json(Service::responseSuccess('Reserva reprogramada correctamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError($e->getMessage() ?: 'Error al reprogramar la reserva.'));
        }
    }

    private function enviarCorreoReprogramacion(Reserva $reserva, string $fechaAnterior, string $horaInicioAnterior, string $horaFinAnterior, string $motivo): void
    {
        try {
            $email = optional($reserva->cliente->usuario)->email;
            if ($email) {
                Mail::to($email)->send(new ReservaReprogramadaMail($reserva, $fechaAnterior, $horaInicioAnterior, $horaFinAnterior, $motivo));
            }
        } catch (Exception $e) {
            Log::error('No se pudo enviar correo de reprogramación de reserva: ' . $e->getMessage());
        }
    }

    private function verificarAcceso(Reserva $reserva): void
    {
        if (Auth::user()->esSuperAdmin()) return;

        $idComplejo = UsuarioComplejo::where('id_usuario', Auth::id())->value('id_complejo');
        $perteneceAlComplejo = $reserva->cancha()
            ->where('id_complejo', $idComplejo)
            ->exists();

        if (!$perteneceAlComplejo) abort(403);
    }
}
