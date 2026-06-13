<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Service;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar la vista de login (administrativos y clientes).
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Procesar el inicio de sesión (AJAX).
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $usuario = Auth::user();

        // Registrar el último acceso
        $usuario->forceFill(['ultimo_acceso_at' => now()])->save();

        // Distinguir por rol: el Cliente (id_rol = 3) va a completar su perfil;
        // los usuarios administrativos van al dashboard.
        $redirect = $usuario->esCliente()
            ? route('cliente.perfil', absolute: false)
            : route('dashboard', absolute: false);

        return response()->json(Service::responseSuccess(
            'Bienvenido a PichangasYa.',
            ['redirect' => $redirect]
        ));
    }

    /**
     * Cerrar sesión.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
