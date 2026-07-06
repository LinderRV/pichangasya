<?php

namespace App\Http\Controllers\Admin\Reporte;

use App\Exports\ReservasExport;
use App\Http\Controllers\Controller;
use App\Helpers\Service;
use App\Models\ComplejoDeportivo;
use App\Models\Reserva;
use App\Models\UsuarioComplejo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ReporteReservaController extends Controller
{
    public function index()
    {
        $complejos = Auth::user()->esSuperAdmin()
            ? ComplejoDeportivo::where('estado', 'activo')->select('id', 'nombre')->orderBy('nombre')->get()
            : collect();

        return view('admin.reporte.reservas', compact('complejos'));
    }

    public function lista(Request $request)
    {
        try {
            return response()->json(Service::responseSuccess('OK', $this->obtenerDatos($request)));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener las reservas.'));
        }
    }

    public function exportar(Request $request)
    {
        return Excel::download(new ReservasExport($this->obtenerDatos($request)), 'reporte_reservas.xlsx');
    }

    private function obtenerDatos(Request $request)
    {
        return $this->consultaFiltrada($request)
            ->with([
                'cliente.usuario:id,nombres,apellidos',
                'cancha:id,nombre,id_complejo',
                'cancha.complejo:id,nombre',
                'estadoReserva:id,nombre',
            ])
            ->orderByDesc('fecha_reserva')
            ->get()
            ->map(fn($r) => [
                'codigo'   => $r->codigo_reserva,
                'cliente'  => trim(optional(optional($r->cliente)->usuario)->nombres . ' ' . optional(optional($r->cliente)->usuario)->apellidos),
                'complejo' => optional(optional($r->cancha)->complejo)->nombre ?? '-',
                'cancha'   => optional($r->cancha)->nombre ?? '-',
                'fecha'    => $r->fecha_reserva,
                'total'    => number_format($r->total, 2),
                'estado'   => optional($r->estadoReserva)->nombre ?? '-',
            ]);
    }

    private function consultaFiltrada(Request $request): Builder
    {
        $request->validate([
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date'],
            'id_complejo' => ['nullable', 'exists:complejo_deportivos,id'],
        ]);

        $fechaDesde = $request->filled('fecha_desde') ? $request->fecha_desde : now()->startOfMonth()->toDateString();
        $fechaHasta = $request->filled('fecha_hasta') ? $request->fecha_hasta : now()->toDateString();

        $query = Reserva::whereBetween('fecha_reserva', [$fechaDesde, $fechaHasta]);

        if (Auth::user()->esSuperAdmin()) {
            if ($request->filled('id_complejo')) {
                $query->whereHas('cancha', fn($q) => $q->where('id_complejo', $request->id_complejo));
            }
        } else {
            $idComplejo = UsuarioComplejo::where('id_usuario', Auth::id())->value('id_complejo');
            $query->whereHas('cancha', fn($q) => $q->where('id_complejo', $idComplejo));
        }

        return $query;
    }
}
