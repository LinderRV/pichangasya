<?php

namespace App\Http\Controllers\Admin\Pago;

use App\Http\Controllers\Controller;
use App\Helpers\Service;
use App\Models\Pago;
use App\Models\UsuarioComplejo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class PagoController extends Controller
{
    public function index()
    {
        return view('admin.pago.index');
    }

    public function lista()
    {
        try {
            $query = Pago::with([
                'reserva.cliente.usuario:id,nombres,apellidos',
                'reserva.cancha:id,nombre,id_complejo',
                'reserva.cancha.complejo:id,nombre',
                'metodoPago:id,nombre',
            ]);

            if (!Auth::user()->esSuperAdmin()) {
                $idComplejo = UsuarioComplejo::where('id_usuario', Auth::id())->value('id_complejo');
                $query->whereHas('reserva.cancha', fn($q) => $q->where('id_complejo', $idComplejo));
            }

            $pagos = $query->orderByDesc('fecha_pago')->get()->map(fn($p) => [
                'id'               => $p->id,
                'codigo_operacion' => $p->codigo_operacion,
                'codigo_reserva'   => optional($p->reserva)->codigo_reserva ?? '-',
                'cliente'          => trim(optional(optional(optional($p->reserva)->cliente)->usuario)->nombres . ' ' . optional(optional(optional($p->reserva)->cliente)->usuario)->apellidos),
                'complejo'         => optional(optional(optional($p->reserva)->cancha)->complejo)->nombre ?? '-',
                'cancha'           => optional(optional($p->reserva)->cancha)->nombre ?? '-',
                'monto'            => number_format($p->monto, 2),
                'metodo_pago'      => optional($p->metodoPago)->nombre ?? '-',
                'estado'           => $p->estado,
                'fecha_pago'       => optional($p->fecha_pago)->format('d/m/Y H:i'),
            ]);

            return response()->json(Service::responseSuccess('OK', $pagos));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener el historial de pagos.'));
        }
    }

    public function obtener($id)
    {
        try {
            $pago = Pago::with([
                'reserva.cliente.usuario',
                'reserva.cancha.complejo',
                'metodoPago',
            ])->find($id);

            if (!$pago) {
                return response()->json(Service::responseError('Pago no encontrado.'), 404);
            }

            if (!Auth::user()->esSuperAdmin()) {
                $idComplejo = UsuarioComplejo::where('id_usuario', Auth::id())->value('id_complejo');
                if (optional(optional($pago->reserva)->cancha)->id_complejo !== $idComplejo) {
                    abort(403);
                }
            }

            return response()->json(Service::responseSuccess('OK', [
                'id'               => $pago->id,
                'codigo_operacion' => $pago->codigo_operacion,
                'codigo_reserva'   => optional($pago->reserva)->codigo_reserva,
                'cliente'          => trim(optional(optional(optional($pago->reserva)->cliente)->usuario)->nombres . ' ' . optional(optional(optional($pago->reserva)->cliente)->usuario)->apellidos),
                'email'            => optional(optional(optional($pago->reserva)->cliente)->usuario)->email,
                'complejo'         => optional(optional(optional($pago->reserva)->cancha)->complejo)->nombre,
                'cancha'           => optional(optional($pago->reserva)->cancha)->nombre,
                'monto'            => $pago->monto,
                'metodo_pago'      => optional($pago->metodoPago)->nombre,
                'estado'           => $pago->estado,
                'fecha_pago'       => optional($pago->fecha_pago)->format('d/m/Y H:i'),
                'comprobante_url'  => $pago->comprobante_url,
            ]));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener el pago.'));
        }
    }
}
