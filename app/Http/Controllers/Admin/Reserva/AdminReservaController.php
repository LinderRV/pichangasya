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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class AdminReservaController extends Controller
{
    public function index()
    {
        return view('admin.reserva.index');
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
            $reserva = Reserva::with('pago')->find($id);
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

            return response()->json(Service::responseSuccess('Reserva cancelada y reembolso registrado.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al cancelar la reserva.'));
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
