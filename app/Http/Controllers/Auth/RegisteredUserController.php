<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        // Crear nuevo usuario con los datos del formulario
        $usuario = Usuario::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'clave' => Hash::make($request->clave),
            'estado' => 'activo',
        ]);

        // Disparar evento de usuario registrado
        event(new Registered($usuario));

        // Iniciar sesión automáticamente
        Auth::login($usuario);

        // Redirigir al dashboard
        return redirect(route('dashboard', absolute: false));
    }
}
