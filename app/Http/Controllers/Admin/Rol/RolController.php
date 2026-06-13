<?php

namespace App\Http\Controllers\Admin\Rol;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\Rol;
use Illuminate\Validation\ValidationException;
use Exception;

class RolController extends Controller
{
    public function index()
    {
        return view('admin.rol.index');
    }

    public function listaRol()
    {
        try {
            $roles = Rol::select('id', 'nombre', 'descripcion')->get();

            return response()->json(Service::responseSuccess('Roles obtenidos correctamente', $roles));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function obtener($id)
    {
        try {
            $rol = Rol::find($id);
            if (!$rol) {
                return response()->json(Service::responseError('Rol no encontrado'), 404);
            }

            return response()->json(Service::responseSuccess('Rol obtenido correctamente', $rol));
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
                'nombre' => ['required', 'string', 'max:85', 'unique:roles,nombre'],
                'descripcion' => ['nullable', 'string', 'max:255'],
            ]);

            $rol = Rol::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);

            return response()->json(Service::responseSuccess('Rol registrado correctamente', $rol));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $rol = Rol::find($id);
            if (!$rol) {
                return response()->json(Service::responseError('Rol no encontrado'), 404);
            }

            $request->validate([
                'nombre' => ['required', 'string', 'max:85', 'unique:roles,nombre,' . $id],
                'descripcion' => ['nullable', 'string', 'max:255'],
            ]);

            $rol->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);

            return response()->json(Service::responseSuccess('Rol actualizado correctamente', $rol));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function eliminar($id)
    {
        try {
            $rol = Rol::find($id);
            if (!$rol) {
                return response()->json(Service::responseError('Rol no encontrado'), 404);
            }

            // Proteger: el rol Super Admin (id = 1) no se puede eliminar
            if ((int) $id === 1) {
                return response()->json(Service::responseError('No se puede eliminar el rol Super Admin.'));
            }

            // No eliminar un rol que tenga usuarios asignados (FK en usuario_rol)
            $enUso = \Illuminate\Support\Facades\DB::table('usuario_rol')->where('id_rol', $id)->exists();
            if ($enUso) {
                return response()->json(Service::responseError('No se puede eliminar: el rol tiene usuarios asignados.'));
            }

            $rol->delete();

            return response()->json(Service::responseSuccess('Rol eliminado correctamente'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }
}
