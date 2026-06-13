<?php

namespace App\Http\Controllers\Complejos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\ComplejoDeportivo;
use App\Models\UsuarioComplejo;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class ComplejoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::select('id', 'nombre')->orderBy('nombre')->get();
        return view('admin.complejo.index', compact('departamentos'));
    }

    public function listaComplejo()
    {
        try {
            $complejos = ComplejoDeportivo::with(['distrito:id,nombre', 'usuarioComplejo.usuario:id,nombres,apellidos'])
                ->get()
                ->map(function ($c) {
                    $dueno = optional(optional($c->usuarioComplejo)->usuario);
                    return [
                        'id' => $c->id,
                        'nombre' => $c->nombre,
                        'correo' => $c->correo,
                        'telefono' => $c->telefono,
                        'distrito' => $c->distrito->nombre ?? '-',
                        'dueno' => $dueno->nombres ? trim($dueno->nombres . ' ' . $dueno->apellidos) : '-',
                        'estado' => $c->estado,
                    ];
                });

            return response()->json(Service::responseSuccess('Complejos obtenidos correctamente', $complejos));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    // Cascada: provincias de un departamento
    public function provincias($idDepartamento)
    {
        try {
            $provincias = Provincia::where('id_departamento', $idDepartamento)
                ->select('id', 'nombre')->orderBy('nombre')->get();
            return response()->json(Service::responseSuccess('Provincias obtenidas', $provincias));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    // Cascada: distritos de una provincia
    public function distritos($idProvincia)
    {
        try {
            $distritos = Distrito::where('id_provincia', $idProvincia)
                ->select('id', 'nombre')->orderBy('nombre')->get();
            return response()->json(Service::responseSuccess('Distritos obtenidos', $distritos));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function obtener($id)
    {
        try {
            $c = ComplejoDeportivo::with('distrito.provincia')->find($id);
            if (!$c) {
                return response()->json(Service::responseError('Complejo no encontrado'), 404);
            }

            $data = [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'descripcion' => $c->descripcion,
                'ruc' => $c->ruc,
                'correo' => $c->correo,
                'direccion' => $c->direccion,
                'telefono' => $c->telefono,
                'imagen' => $c->imagen,
                'estado' => $c->estado,
                'id_distrito' => $c->id_distrito,
                'id_provincia' => optional($c->distrito)->id_provincia,
                'id_departamento' => optional(optional($c->distrito)->provincia)->id_departamento,
            ];

            return response()->json(Service::responseSuccess('Complejo obtenido correctamente', $data));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function guardar(Request $request)
    {
        try {
            $request->validate([
                'nombre' => ['required', 'string', 'max:255', 'unique:complejo_deportivos,nombre'],
                'correo' => ['required', 'email', 'max:255', 'unique:complejo_deportivos,correo'],
                'id_distrito' => ['required', 'exists:distritos,id'],
                'descripcion' => ['nullable', 'string', 'max:255'],
                'ruc' => ['nullable', 'string', 'max:255', 'unique:complejo_deportivos,ruc'],
                'direccion' => ['nullable', 'string', 'max:255'],
                'telefono' => ['nullable', 'string', 'max:20'],
                'estado' => ['required', 'in:activo,inactivo'],
                'imagen' => ['nullable', 'image', 'max:2048'],
            ], [
                'nombre.unique' => 'Ya existe un complejo con ese nombre.',
                'correo.unique' => 'Ya existe un complejo con ese correo.',
                'ruc.unique' => 'Ya existe un complejo con ese RUC.',
            ]);

            $datos = $request->only(['nombre', 'descripcion', 'ruc', 'correo', 'direccion', 'telefono', 'estado', 'id_distrito']);

            if ($request->hasFile('imagen')) {
                $datos['imagen'] = $this->subirImagen($request->file('imagen'));
            }

            ComplejoDeportivo::create($datos);

            return response()->json(Service::responseSuccess('Complejo registrado correctamente'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $complejo = ComplejoDeportivo::find($id);
            if (!$complejo) {
                return response()->json(Service::responseError('Complejo no encontrado'), 404);
            }

            $request->validate([
                'nombre' => ['required', 'string', 'max:255', 'unique:complejo_deportivos,nombre,' . $id],
                'correo' => ['required', 'email', 'max:255', 'unique:complejo_deportivos,correo,' . $id],
                'id_distrito' => ['required', 'exists:distritos,id'],
                'descripcion' => ['nullable', 'string', 'max:255'],
                'ruc' => ['nullable', 'string', 'max:255', 'unique:complejo_deportivos,ruc,' . $id],
                'direccion' => ['nullable', 'string', 'max:255'],
                'telefono' => ['nullable', 'string', 'max:20'],
                'estado' => ['required', 'in:activo,inactivo'],
                'imagen' => ['nullable', 'image', 'max:2048'],
            ], [
                'nombre.unique' => 'Ya existe un complejo con ese nombre.',
                'correo.unique' => 'Ya existe un complejo con ese correo.',
                'ruc.unique' => 'Ya existe un complejo con ese RUC.',
            ]);

            $datos = $request->only(['nombre', 'descripcion', 'ruc', 'correo', 'direccion', 'telefono', 'estado', 'id_distrito']);

            if ($request->hasFile('imagen')) {
                $this->eliminarImagen($complejo->imagen);
                $datos['imagen'] = $this->subirImagen($request->file('imagen'));
            }

            $complejo->update($datos);

            return response()->json(Service::responseSuccess('Complejo actualizado correctamente'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function eliminar($id)
    {
        try {
            $complejo = ComplejoDeportivo::find($id);
            if (!$complejo) {
                return response()->json(Service::responseError('Complejo no encontrado'), 404);
            }

            // No eliminar si tiene canchas registradas (FK)
            $tieneCanchas = DB::table('canchas')->where('id_complejo', $id)->exists();
            if ($tieneCanchas) {
                return response()->json(Service::responseError('No se puede eliminar: el complejo tiene canchas registradas.'));
            }

            DB::transaction(function () use ($complejo) {
                UsuarioComplejo::where('id_complejo', $complejo->id)->delete();
                $this->eliminarImagen($complejo->imagen);
                $complejo->delete();
            });

            return response()->json(Service::responseSuccess('Complejo eliminado correctamente'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    /** Sube la imagen a public/uploads/complejos y devuelve la ruta relativa */
    private function subirImagen($archivo): string
    {
        $nombre = uniqid('complejo_') . '.' . $archivo->getClientOriginalExtension();
        $destino = public_path('uploads/complejos');
        if (!file_exists($destino)) {
            mkdir($destino, 0755, true);
        }
        $archivo->move($destino, $nombre);
        return 'uploads/complejos/' . $nombre;
    }

    /** Elimina una imagen del disco si existe */
    private function eliminarImagen(?string $ruta): void
    {
        if ($ruta && file_exists(public_path($ruta))) {
            @unlink(public_path($ruta));
        }
    }
}
