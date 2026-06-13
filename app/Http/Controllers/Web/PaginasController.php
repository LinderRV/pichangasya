<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Helpers\Service;
use App\Models\Cancha;
use App\Models\Distrito;
use App\Models\TipoCancha;
use App\Models\MetodoPago;
use App\Services\DisponibilidadService;
use Illuminate\Http\Request;

class PaginasController extends Controller
{
    public function __construct(private DisponibilidadService $disponibilidad) {}

    public function inicio()
    {
        $distritos   = Distrito::select('id', 'nombre')->orderBy('nombre')->get();
        $tipoCanchas = TipoCancha::select('id', 'nombre')->orderBy('nombre')->get();

        $canchas = Cancha::with(['tipoCancha:id,nombre', 'complejo.distrito:id,nombre'])
            ->where('estado', 'activo')
            ->latest()
            ->take(8)
            ->get();

        return view('web.paginas.inicio', compact('distritos', 'tipoCanchas', 'canchas'));
    }

    public function canchas(Request $request)
    {
        $distritos   = Distrito::select('id', 'nombre')->orderBy('nombre')->get();
        $tipoCanchas = TipoCancha::select('id', 'nombre')->orderBy('nombre')->get();

        $query = Cancha::with(['tipoCancha:id,nombre', 'complejo.distrito:id,nombre'])
            ->where('estado', 'activo');

        if ($request->filled('id_tipo_cancha')) {
            $query->where('id_tipo_cancha', $request->id_tipo_cancha);
        }

        if ($request->filled('id_distrito')) {
            $query->whereHas('complejo', fn($q) => $q->where('id_distrito', $request->id_distrito));
        }

        $canchas = $query->get();

        return view('web.paginas.canchas', compact('distritos', 'tipoCanchas', 'canchas'));
    }

    public function cancha($id)
    {
        $cancha = Cancha::with(['tipoCancha:id,nombre', 'complejo.distrito.provincia'])
            ->where('estado', 'activo')
            ->findOrFail($id);

        $metodosPago = MetodoPago::where('estado', 'activo')->select('id', 'nombre')->get();

        return view('web.paginas.cancha', compact('cancha', 'metodosPago'));
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
        } catch (\Exception $e) {
            return response()->json(Service::responseError('Error al consultar disponibilidad.'));
        }
    }
}
