<?php

namespace App\Http\Controllers\Admin\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class UsuarioController extends Controller
{
    public function index()
    {
        $roles = Rol::select('id', 'nombre')->get();
        return view('admin.usuario.index', compact('roles'));
    }

    public function listaUsuario()
    {
        try {
            $usuarios = Usuario::with('roles:id,nombre')
                ->select('id', 'nombres', 'apellidos', 'email', 'telefono', 'sexo', 'estado')
                ->get()
                ->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'nombres' => $u->nombres,
                        'apellidos' => $u->apellidos,
                        'email' => $u->email,
                        'telefono' => $u->telefono,
                        'sexo' => $u->sexo,
                        'estado' => $u->estado,
                        'rol' => $u->roles->first()->nombre ?? '-',
                        'id_rol' => $u->roles->first()->id ?? null,
                    ];
                });

            return response()->json(Service::responseSuccess('Usuarios obtenidos correctamente', $usuarios));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function obtener($id)
    {
        try {
            $usuario = Usuario::with('roles:id')->find($id);
            if (!$usuario) {
                return response()->json(Service::responseError('Usuario no encontrado'), 404);
            }

            $data = [
                'id' => $usuario->id,
                'nombres' => $usuario->nombres,
                'apellidos' => $usuario->apellidos,
                'email' => $usuario->email,
                'telefono' => $usuario->telefono,
                'sexo' => $usuario->sexo,
                'estado' => $usuario->estado,
                'id_rol' => $usuario->roles->first()->id ?? null,
            ];

            return response()->json(Service::responseSuccess('Usuario obtenido correctamente', $data));
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
                'nombres' => ['required', 'string', 'max:255'],
                'apellidos' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:200', 'unique:usuarios,email'],
                'password' => ['required', 'string', 'min:8'],
                'telefono' => ['nullable', 'string', 'max:20'],
                'sexo' => ['nullable', 'in:masculino,femenino'],
                'estado' => ['required', 'in:activo,inactivo'],
                'id_rol' => ['required', 'exists:roles,id'],
            ]);

            $usuario = Usuario::create([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'sexo' => $request->sexo,
                'estado' => $request->estado,
            ]);

            $usuario->roles()->sync([$request->id_rol]);

            return response()->json(Service::responseSuccess('Usuario registrado correctamente', $usuario));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $usuario = Usuario::find($id);
            if (!$usuario) {
                return response()->json(Service::responseError('Usuario no encontrado'), 404);
            }

            $request->validate([
                'nombres' => ['required', 'string', 'max:255'],
                'apellidos' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:200', 'unique:usuarios,email,' . $id],
                'password' => ['nullable', 'string', 'min:8'],
                'telefono' => ['nullable', 'string', 'max:20'],
                'sexo' => ['nullable', 'in:masculino,femenino'],
                'estado' => ['required', 'in:activo,inactivo'],
                'id_rol' => ['required', 'exists:roles,id'],
            ]);

            $datos = [
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'sexo' => $request->sexo,
                'estado' => $request->estado,
            ];

            // Solo actualizar la clave si la mandaron
            if ($request->filled('password')) {
                $datos['password'] = Hash::make($request->password);
            }

            $usuario->update($datos);
            $usuario->roles()->sync([$request->id_rol]);

            return response()->json(Service::responseSuccess('Usuario actualizado correctamente', $usuario));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function eliminar($id)
    {
        try {
            $usuario = Usuario::with('roles:id')->find($id);
            if (!$usuario) {
                return response()->json(Service::responseError('Usuario no encontrado'), 404);
            }

            // Proteger: un usuario con rol Super Admin (id_rol = 1) no se puede eliminar
            if ($usuario->roles->contains('id', 1)) {
                return response()->json(Service::responseError('No se puede eliminar un usuario Super Admin.'));
            }

            // Bloquear si tiene datos transaccionales (no se deben perder)
            $tieneHistorial = DB::table('historial_estado_reservas')->where('id_usuario', $id)->exists();
            $tieneReembolsos = DB::table('reembolsos')->where('id_usuario', $id)->exists();
            $tieneCancelaciones = DB::table('reservas')->where('id_usuario_cancelado', $id)->exists();
            if ($tieneHistorial || $tieneReembolsos || $tieneCancelaciones) {
                return response()->json(Service::responseError('No se puede eliminar: el usuario tiene actividad registrada en el sistema.'));
            }

            if ($usuario->cliente) {
                $tieneReservas = DB::table('reservas')->where('id_cliente', $usuario->cliente->id)->exists();
                if ($tieneReservas) {
                    return response()->json(Service::responseError('No se puede eliminar: el cliente tiene reservas registradas.'));
                }
            }

            // Eliminar vínculos de pertenencia y el usuario (FK en MySQL) dentro de una transacción
            DB::transaction(function () use ($usuario) {
                if ($usuario->cliente) {
                    $usuario->cliente->delete();
                }
                DB::table('usuario_complejos')->where('id_usuario', $usuario->id)->delete();
                $usuario->roles()->detach();
                $usuario->delete();
            });

            return response()->json(Service::responseSuccess('Usuario eliminado correctamente'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }
}
