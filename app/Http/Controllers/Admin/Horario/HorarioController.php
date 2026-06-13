<?php

namespace App\Http\Controllers\Admin\Horario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\HorarioConfigurado;
use App\Models\Cancha;
use App\Models\UsuarioComplejo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class HorarioController extends Controller
{
    public function index()
    {
        $canchas = $this->canchasDelUsuario();
        return view('admin.horario.index', compact('canchas'));
    }

    public function lista()
    {
        try {
            $idComplejo = $this->idComplejo();

            $query = HorarioConfigurado::with(['cancha:id,nombre,id_complejo']);

            if ($idComplejo) {
                $query->whereHas('cancha', fn($q) => $q->where('id_complejo', $idComplejo));
            }

            $horarios = $query->get()->map(fn($h) => [
                'id'                => $h->id,
                'cancha'            => optional($h->cancha)->nombre ?? '-',
                'dia_semana'        => $h->dia_semana,
                'hora_inicio'       => substr($h->hora_inicio, 0, 5),
                'hora_fin'          => substr($h->hora_fin, 0, 5),
                'intervalo_minutos' => $h->intervalo_minutos . ' min',
                'estado'            => $h->estado,
            ]);

            return response()->json(Service::responseSuccess('OK', $horarios));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener horarios.'));
        }
    }

    public function obtener($id)
    {
        try {
            $horario = HorarioConfigurado::find($id);
            if (!$horario) {
                return response()->json(Service::responseError('Horario no encontrado.'), 404);
            }

            $this->verificarAcceso($horario->id_cancha);

            return response()->json(Service::responseSuccess('OK', [
                'id'                => $horario->id,
                'id_cancha'         => $horario->id_cancha,
                'dia_semana'        => $horario->dia_semana,
                'hora_inicio'       => substr($horario->hora_inicio, 0, 5),
                'hora_fin'          => substr($horario->hora_fin, 0, 5),
                'intervalo_minutos' => $horario->intervalo_minutos,
                'estado'            => $horario->estado,
            ]));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener el horario.'));
        }
    }

    public function guardar(Request $request)
    {
        try {
            $request->validate([
                'id_cancha'         => ['required', 'exists:canchas,id'],
                'dia_semana'        => ['required', 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo'],
                'hora_inicio'       => ['required', 'date_format:H:i'],
                'hora_fin'          => ['required', 'date_format:H:i', 'after:hora_inicio'],
                'intervalo_minutos' => ['required', 'integer', 'in:30,60,90,120'],
                'estado'            => ['required', 'in:activo,inactivo'],
            ], [
                'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            ]);

            $this->verificarAcceso($request->id_cancha);

            HorarioConfigurado::create($request->only([
                'id_cancha', 'dia_semana', 'hora_inicio', 'hora_fin', 'intervalo_minutos', 'estado',
            ]));

            return response()->json(Service::responseSuccess('Horario registrado correctamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al registrar el horario.'));
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $horario = HorarioConfigurado::find($id);
            if (!$horario) {
                return response()->json(Service::responseError('Horario no encontrado.'), 404);
            }

            $request->validate([
                'id_cancha'         => ['required', 'exists:canchas,id'],
                'dia_semana'        => ['required', 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo'],
                'hora_inicio'       => ['required', 'date_format:H:i'],
                'hora_fin'          => ['required', 'date_format:H:i', 'after:hora_inicio'],
                'intervalo_minutos' => ['required', 'integer', 'in:30,60,90,120'],
                'estado'            => ['required', 'in:activo,inactivo'],
            ], [
                'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            ]);

            $this->verificarAcceso($request->id_cancha);

            $horario->update($request->only([
                'id_cancha', 'dia_semana', 'hora_inicio', 'hora_fin', 'intervalo_minutos', 'estado',
            ]));

            return response()->json(Service::responseSuccess('Horario actualizado correctamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al actualizar el horario.'));
        }
    }

    public function eliminar($id)
    {
        try {
            $horario = HorarioConfigurado::find($id);
            if (!$horario) {
                return response()->json(Service::responseError('Horario no encontrado.'), 404);
            }

            $this->verificarAcceso($horario->id_cancha);

            $horario->delete();

            return response()->json(Service::responseSuccess('Horario eliminado correctamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al eliminar el horario.'));
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
