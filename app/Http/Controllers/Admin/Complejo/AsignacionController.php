<?php

namespace App\Http\Controllers\Admin\Complejo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\UsuarioComplejo;
use App\Models\ComplejoDeportivo;
use App\Models\Usuario;
use Illuminate\Validation\ValidationException;
use Exception;

class AsignacionController extends Controller
{
    public function index()
    {
        return view('admin.complejo.asignacion');
    }

    public function lista()
    {
        try {
            $asignaciones = UsuarioComplejo::with(['complejo:id,nombre', 'usuario:id,nombres,apellidos'])
                ->get()
                ->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'complejo' => $a->complejo->nombre ?? '-',
                        'dueno' => $a->usuario ? trim($a->usuario->nombres . ' ' . $a->usuario->apellidos) : '-',
                        'cargo' => $a->cargo,
                        'estado' => $a->estado,
                    ];
                });

            return response()->json(Service::responseSuccess('Asignaciones obtenidas correctamente', $asignaciones));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    // Un complejo puede tener varios usuarios (1 Dueño + N Empleados): se listan todos
    public function complejosDisponibles()
    {
        try {
            $complejos = ComplejoDeportivo::select('id', 'nombre')->orderBy('nombre')->get();
            return response()->json(Service::responseSuccess('Complejos disponibles', $complejos));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    // Todos los Usuarios Internos (rol 2): un usuario puede estar en varios complejos
    public function usuariosDisponibles()
    {
        try {
            $usuarios = Usuario::select('usuarios.id', 'usuarios.nombres', 'usuarios.apellidos')
                ->join('usuario_rol', 'usuario_rol.id_usuario', '=', 'usuarios.id')
                ->where('usuario_rol.id_rol', 2)
                ->orderBy('usuarios.nombres')
                ->get()
                ->map(fn ($u) => [
                    'id' => $u->id,
                    'nombre' => trim($u->nombres . ' ' . $u->apellidos),
                ]);

            return response()->json(Service::responseSuccess('Usuarios disponibles', $usuarios));
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function obtener($id)
    {
        try {
            $a = UsuarioComplejo::find($id);
            if (!$a) {
                return response()->json(Service::responseError('Asignación no encontrada'), 404);
            }

            return response()->json(Service::responseSuccess('Asignación obtenida correctamente', $a));
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
                'id_complejo' => ['required', 'exists:complejo_deportivos,id'],
                'id_usuario' => ['required', 'exists:usuarios,id'],
                'cargo' => ['required', 'in:Dueño,Empleado'],
                'estado' => ['required', 'in:activo,inactivo'],
            ]);

            // El mismo usuario no puede estar dos veces en el MISMO complejo
            if (UsuarioComplejo::where('id_complejo', $request->id_complejo)->where('id_usuario', $request->id_usuario)->exists()) {
                return response()->json(Service::responseError('Este usuario ya pertenece a este complejo.'));
            }

            // Un complejo solo puede tener UN Dueño
            if ($request->cargo === 'Dueño' && UsuarioComplejo::where('id_complejo', $request->id_complejo)->where('cargo', 'Dueño')->exists()) {
                return response()->json(Service::responseError('Este complejo ya tiene un Dueño asignado.'));
            }

            UsuarioComplejo::create($request->only(['id_complejo', 'id_usuario', 'cargo', 'estado']));

            return response()->json(Service::responseSuccess('Dueño asignado correctamente'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $a = UsuarioComplejo::find($id);
            if (!$a) {
                return response()->json(Service::responseError('Asignación no encontrada'), 404);
            }

            $request->validate([
                'id_complejo' => ['required', 'exists:complejo_deportivos,id'],
                'id_usuario' => ['required', 'exists:usuarios,id'],
                'cargo' => ['required', 'in:Dueño,Empleado'],
                'estado' => ['required', 'in:activo,inactivo'],
            ]);

            // El mismo usuario no puede estar dos veces en el MISMO complejo (ignorando esta fila)
            if (UsuarioComplejo::where('id_complejo', $request->id_complejo)->where('id_usuario', $request->id_usuario)->where('id', '!=', $id)->exists()) {
                return response()->json(Service::responseError('Este usuario ya pertenece a este complejo.'));
            }

            // Un complejo solo puede tener UN Dueño (ignorando esta misma fila)
            if ($request->cargo === 'Dueño' && UsuarioComplejo::where('id_complejo', $request->id_complejo)->where('cargo', 'Dueño')->where('id', '!=', $id)->exists()) {
                return response()->json(Service::responseError('Este complejo ya tiene un Dueño asignado.'));
            }

            $a->update($request->only(['id_complejo', 'id_usuario', 'cargo', 'estado']));

            return response()->json(Service::responseSuccess('Asignación actualizada correctamente'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function eliminar($id)
    {
        try {
            $a = UsuarioComplejo::find($id);
            if (!$a) {
                return response()->json(Service::responseError('Asignación no encontrada'), 404);
            }

            $a->delete();

            return response()->json(Service::responseSuccess('Asignación eliminada correctamente'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }
}
