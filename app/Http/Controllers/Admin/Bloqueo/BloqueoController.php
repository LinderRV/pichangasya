<?php

namespace App\Http\Controllers\Admin\Bloqueo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\BloqueoCancha;
use App\Models\Cancha;
use App\Models\UsuarioComplejo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class BloqueoController extends Controller
{
    public function index()
    {
        $canchas = $this->canchasDelUsuario();
        return view('admin.bloqueo.index', compact('canchas'));
    }

    public function lista()
    {
        try {
            $idComplejo = $this->idComplejo();

            $query = BloqueoCancha::with(['cancha:id,nombre,id_complejo']);

            if ($idComplejo) {
                $query->whereHas('cancha', fn($q) => $q->where('id_complejo', $idComplejo));
            }

            $bloqueos = $query->orderBy('fecha')->orderBy('hora_inicio')->get()->map(fn($b) => [
                'id'          => $b->id,
                'cancha'      => optional($b->cancha)->nombre ?? '-',
                'fecha'       => $b->fecha,
                'hora_inicio' => substr($b->hora_inicio, 0, 5),
                'hora_fin'    => substr($b->hora_fin, 0, 5),
                'motivo'      => $b->motivo,
                'descripcion' => $b->descripcion ?? '-',
            ]);

            return response()->json(Service::responseSuccess('OK', $bloqueos));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener los bloqueos.'));
        }
    }

    public function obtener($id)
    {
        try {
            $bloqueo = BloqueoCancha::find($id);
            if (!$bloqueo) {
                return response()->json(Service::responseError('Bloqueo no encontrado.'), 404);
            }

            $this->verificarAcceso($bloqueo->id_cancha);

            return response()->json(Service::responseSuccess('OK', [
                'id'          => $bloqueo->id,
                'id_cancha'   => $bloqueo->id_cancha,
                'fecha'       => $bloqueo->fecha,
                'hora_inicio' => substr($bloqueo->hora_inicio, 0, 5),
                'hora_fin'    => substr($bloqueo->hora_fin, 0, 5),
                'motivo'      => $bloqueo->motivo,
                'descripcion' => $bloqueo->descripcion,
            ]));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener el bloqueo.'));
        }
    }

    public function guardar(Request $request)
    {
        try {
            $request->validate([
                'id_cancha'   => ['required', 'exists:canchas,id'],
                'fecha'       => ['required', 'date', 'after_or_equal:today'],
                'hora_inicio' => ['required', 'date_format:H:i'],
                'hora_fin'    => ['required', 'date_format:H:i', 'after:hora_inicio'],
                'motivo'      => ['required', 'in:mantenimiento,evento_especial,otro'],
                'descripcion' => ['nullable', 'string', 'max:255'],
            ], [
                'fecha.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
                'hora_fin.after'       => 'La hora de fin debe ser posterior a la hora de inicio.',
            ]);

            $this->verificarAcceso($request->id_cancha);

            BloqueoCancha::create($request->only([
                'id_cancha', 'fecha', 'hora_inicio', 'hora_fin', 'motivo', 'descripcion',
            ]));

            return response()->json(Service::responseSuccess('Bloqueo registrado correctamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al registrar el bloqueo.'));
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $bloqueo = BloqueoCancha::find($id);
            if (!$bloqueo) {
                return response()->json(Service::responseError('Bloqueo no encontrado.'), 404);
            }

            $request->validate([
                'id_cancha'   => ['required', 'exists:canchas,id'],
                'fecha'       => ['required', 'date'],
                'hora_inicio' => ['required', 'date_format:H:i'],
                'hora_fin'    => ['required', 'date_format:H:i', 'after:hora_inicio'],
                'motivo'      => ['required', 'in:mantenimiento,evento_especial,otro'],
                'descripcion' => ['nullable', 'string', 'max:255'],
            ], [
                'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            ]);

            $this->verificarAcceso($request->id_cancha);

            $bloqueo->update($request->only([
                'id_cancha', 'fecha', 'hora_inicio', 'hora_fin', 'motivo', 'descripcion',
            ]));

            return response()->json(Service::responseSuccess('Bloqueo actualizado correctamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al actualizar el bloqueo.'));
        }
    }

    public function eliminar($id)
    {
        try {
            $bloqueo = BloqueoCancha::find($id);
            if (!$bloqueo) {
                return response()->json(Service::responseError('Bloqueo no encontrado.'), 404);
            }

            $this->verificarAcceso($bloqueo->id_cancha);
            $bloqueo->delete();

            return response()->json(Service::responseSuccess('Bloqueo eliminado correctamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al eliminar el bloqueo.'));
        }
    }

    private function idComplejo(): ?int
    {
        if (Auth::user()->esSuperAdmin()) return null;
        return UsuarioComplejo::where('id_usuario', Auth::id())->value('id_complejo');
    }

    private function canchasDelUsuario()
    {
        $idComplejo = $this->idComplejo();
        $query = Cancha::where('estado', 'activo')->select('id', 'nombre');
        if ($idComplejo) {
            $query->where('id_complejo', $idComplejo);
        }
        return $query->orderBy('nombre')->get();
    }

    private function verificarAcceso(int $idCancha): void
    {
        if (Auth::user()->esSuperAdmin()) return;

        $idComplejo = $this->idComplejo();
        $perteneceAlComplejo = Cancha::where('id', $idCancha)
            ->where('id_complejo', $idComplejo)
            ->exists();

        if (!$perteneceAlComplejo) {
            abort(403);
        }
    }
}
