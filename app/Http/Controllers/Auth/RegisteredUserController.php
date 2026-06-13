<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Service;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Mostrar la vista de registro (solo clientes).
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Registrar un nuevo cliente (AJAX).
     *
     * Crea el usuario y le asigna el rol Cliente (id_rol = 3).
     * Los datos de la tabla clientes se completan luego desde el perfil.
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        // Crear el usuario
        $usuario = Usuario::create([
            'nombres'   => $request->nombres,
            'apellidos' => $request->apellidos,
            'email'     => $request->email,
            'password'  => Hash::make($request->clave),
            'estado'    => 'activo',
        ]);

        // Asignar el rol de Cliente (id_rol = 3) en usuario_rol
        DB::table('usuario_rol')->insert([
            'id_usuario' => $usuario->id,
            'id_rol'     => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Disparar evento e iniciar sesión automáticamente
        event(new Registered($usuario));
        Auth::login($usuario);

        // Redirigir al perfil para que complete sus datos de cliente
        return response()->json(Service::responseSuccess(
            'Bienvenido a PichangasYa. Completa tu información de perfil.',
            ['redirect' => route('cliente.perfil', absolute: false)]
        ));
    }
}
