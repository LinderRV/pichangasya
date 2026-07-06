<?php

namespace App\Http\Controllers\Admin\Reporte;

use App\Exports\IngresosExport;
use App\Http\Controllers\Controller;
use App\Helpers\Service;
use App\Models\ComplejoDeportivo;
use App\Models\Pago;
use App\Models\UsuarioComplejo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Exception;

class ReporteIngresoController extends Controller
{
    public function index()
    {
        $complejos = Auth::user()->esSuperAdmin()
            ? ComplejoDeportivo::where('estado', 'activo')->select('id', 'nombre')->orderBy('nombre')->get()
            : collect();

        return view('admin.reporte.ingresos', compact('complejos'));
    }

    public function lista(Request $request)
    {
        try {
            return response()->json(Service::responseSuccess('OK', $this->obtenerDatos($request)));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener los ingresos.'));
        }
    }

    public function exportar(Request $request)
    {
        return Excel::download(new IngresosExport($this->obtenerDatos($request)), 'reporte_ingresos.xlsx');
    }

    private function obtenerDatos(Request $request)
    {
        return $this->consultaFiltrada($request)
            ->selectRaw('DATE(fecha_pago) as fecha, COUNT(*) as cantidad, SUM(monto) as total')
            ->groupBy('fecha')
            ->orderByDesc('fecha')
            ->get()
            ->map(fn($f) => [
                'fecha'    => Carbon::parse($f->fecha)->format('d/m/Y'),
                'cantidad' => $f->cantidad,
                'total'    => number_format($f->total, 2),
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

        $query = Pago::where('estado', 'confirmado')
            ->whereDate('fecha_pago', '>=', $fechaDesde)
            ->whereDate('fecha_pago', '<=', $fechaHasta);

        if (Auth::user()->esSuperAdmin()) {
            if ($request->filled('id_complejo')) {
                $query->whereHas('reserva.cancha', fn($q) => $q->where('id_complejo', $request->id_complejo));
            }
        } else {
            $idComplejo = UsuarioComplejo::where('id_usuario', Auth::id())->value('id_complejo');
            $query->whereHas('reserva.cancha', fn($q) => $q->where('id_complejo', $idComplejo));
        }

        return $query;
    }
}
