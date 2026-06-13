<?php

namespace App\Http\Controllers\Admin\Cancha;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\Cancha;
use App\Models\TipoCancha;
use App\Models\ComplejoDeportivo;
use App\Models\UsuarioComplejo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class CanchaController extends Controller
{
    public function index()
    {
        $tipoCanchas = TipoCancha::select('id', 'nombre')->orderBy('nombre')->get();
        $complejos   = Auth::user()->esSuperAdmin()
            ? ComplejoDeportivo::where('estado', 'activo')->select('id', 'nombre')->orderBy('nombre')->get()
            : collect();

        return view('admin.cancha.index', compact('tipoCanchas', 'complejos'));
    }

    public function lista()
    {
        try {
            $query = Cancha::with(['tipoCancha:id,nombre', 'complejo:id,nombre']);

            if (!Auth::user()->esSuperAdmin()) {
                $idComplejo = UsuarioComplejo::where('id_usuario', Auth::id())->value('id_complejo');
                $query->where('id_complejo', $idComplejo);
            }

            $canchas = $query->get()->map(fn($c) => [
                'id'          => $c->id,
                'nombre'      => $c->nombre,
                'tipo'        => optional($c->tipoCancha)->nombre ?? '-',
                'complejo'    => optional($c->complejo)->nombre ?? '-',
                'precio_hora' => number_format($c->precio_hora, 2),
                'capacidad'   => $c->capacidad ?? '-',
                'estado'      => $c->estado,
            ]);

            return response()->json(Service::responseSuccess('OK', $canchas));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener canchas.'));
        }
    }

    public function obtener($id)
    {
        try {
            $cancha = Cancha::find($id);
            if (!$cancha) {
                return response()->json(Service::responseError('Cancha no encontrada.'), 404);
            }

            $this->verificarAcceso($cancha);

            return response()->json(Service::responseSuccess('OK', [
                'id'            => $cancha->id,
                'id_complejo'   => $cancha->id_complejo,
                'id_tipo_cancha'=> $cancha->id_tipo_cancha,
                'nombre'        => $cancha->nombre,
                'descripcion'   => $cancha->descripcion,
                'precio_hora'   => $cancha->precio_hora,
                'capacidad'     => $cancha->capacidad,
                'foto'          => $cancha->foto,
                'estado'        => $cancha->estado,
            ]));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al obtener la cancha.'));
        }
    }

    public function guardar(Request $request)
    {
        try {
            $idComplejo = $this->resolverComplejo($request);

            $request->validate([
                'id_tipo_cancha' => ['required', 'exists:tipo_canchas,id'],
                'nombre'         => ['required', 'string', 'max:255', 'unique:canchas,nombre'],
                'descripcion'    => ['nullable', 'string', 'max:255'],
                'precio_hora'    => ['required', 'numeric', 'min:0'],
                'capacidad'      => ['nullable', 'integer', 'min:1'],
                'estado'         => ['required', 'in:activo,inactivo,mantenimiento'],
                'foto'           => ['nullable', 'image', 'max:2048'],
            ], [
                'nombre.unique' => 'Ya existe una cancha con ese nombre.',
            ]);

            $datos = $request->only(['id_tipo_cancha', 'nombre', 'descripcion', 'precio_hora', 'capacidad', 'estado']);
            $datos['id_complejo'] = $idComplejo;

            if ($request->hasFile('foto')) {
                $datos['foto'] = $this->subirFoto($request->file('foto'));
            }

            Cancha::create($datos);

            return response()->json(Service::responseSuccess('Cancha registrada correctamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al registrar la cancha.'));
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $cancha = Cancha::find($id);
            if (!$cancha) {
                return response()->json(Service::responseError('Cancha no encontrada.'), 404);
            }

            $this->verificarAcceso($cancha);
            $idComplejo = $this->resolverComplejo($request);

            $request->validate([
                'id_tipo_cancha' => ['required', 'exists:tipo_canchas,id'],
                'nombre'         => ['required', 'string', 'max:255', 'unique:canchas,nombre,' . $id],
                'descripcion'    => ['nullable', 'string', 'max:255'],
                'precio_hora'    => ['required', 'numeric', 'min:0'],
                'capacidad'      => ['nullable', 'integer', 'min:1'],
                'estado'         => ['required', 'in:activo,inactivo,mantenimiento'],
                'foto'           => ['nullable', 'image', 'max:2048'],
            ], [
                'nombre.unique' => 'Ya existe una cancha con ese nombre.',
            ]);

            $datos = $request->only(['id_tipo_cancha', 'nombre', 'descripcion', 'precio_hora', 'capacidad', 'estado']);
            $datos['id_complejo'] = $idComplejo;

            if ($request->hasFile('foto')) {
                $this->eliminarFoto($cancha->foto);
                $datos['foto'] = $this->subirFoto($request->file('foto'));
            }

            $cancha->update($datos);

            return response()->json(Service::responseSuccess('Cancha actualizada correctamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al actualizar la cancha.'));
        }
    }

    public function eliminar($id)
    {
        try {
            $cancha = Cancha::find($id);
            if (!$cancha) {
                return response()->json(Service::responseError('Cancha no encontrada.'), 404);
            }

            $this->verificarAcceso($cancha);

            $tieneRelaciones = \DB::table('horario_configurados')->where('id_cancha', $id)->exists()
                || \DB::table('reservas')->where('id_cancha', $id)->exists()
                || \DB::table('bloqueo_canchas')->where('id_cancha', $id)->exists();

            if ($tieneRelaciones) {
                return response()->json(Service::responseError('No se puede eliminar: la cancha tiene registros asociados.'));
            }

            $this->eliminarFoto($cancha->foto);
            $cancha->delete();

            return response()->json(Service::responseSuccess('Cancha eliminada correctamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error al eliminar la cancha.'));
        }
    }

    private function resolverComplejo(Request $request): int
    {
        if (Auth::user()->esSuperAdmin()) {
            $request->validate(['id_complejo' => ['required', 'exists:complejo_deportivos,id']]);
            return (int) $request->id_complejo;
        }

        $idComplejo = UsuarioComplejo::where('id_usuario', Auth::id())->value('id_complejo');
        if (!$idComplejo) {
            throw new Exception('No tienes un complejo asignado.');
        }
        return $idComplejo;
    }

    private function verificarAcceso(Cancha $cancha): void
    {
        if (Auth::user()->esSuperAdmin()) return;

        $idComplejo = UsuarioComplejo::where('id_usuario', Auth::id())->value('id_complejo');
        if ($cancha->id_complejo !== $idComplejo) {
            abort(403);
        }
    }

    private function subirFoto($archivo): string
    {
        $nombre  = uniqid('cancha_') . '.' . $archivo->getClientOriginalExtension();
        $destino = public_path('uploads/canchas');
        if (!file_exists($destino)) {
            mkdir($destino, 0755, true);
        }
        $archivo->move($destino, $nombre);
        return 'uploads/canchas/' . $nombre;
    }

    private function eliminarFoto(?string $ruta): void
    {
        if ($ruta && file_exists(public_path($ruta))) {
            @unlink(public_path($ruta));
        }
    }
}
