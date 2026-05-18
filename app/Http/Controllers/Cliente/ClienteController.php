<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    /**
     * Middleware para asegurar que el usuario esté autenticado
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar el perfil del cliente
     */
    public function perfil()
    {
        $usuario = Auth::user();
        
        // Obtener o crear cliente
        $cliente = $usuario->cliente ?? new Cliente();
        
        return view('cliente.perfil', compact('usuario', 'cliente'));
    }

    /**
     * Actualizar el perfil del cliente
     */
    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user();

        // Validar datos
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:200', 'unique:usuarios,email,' . $usuario->id],
            'telefono' => ['nullable', 'string', 'max:20'],
            'sexo' => ['nullable', 'in:masculino,femenino'],
            'documento_identidad' => ['nullable', 'string', 'max:20', 'unique:clientes,documento_identidad,' . ($usuario->cliente->id ?? null)],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        // Actualizar datos del usuario
        $usuario->update([
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'sexo' => $validated['sexo'] ?? null,
        ]);

        // Obtener o crear cliente
        $cliente = $usuario->cliente ?? new Cliente(['id_usuario' => $usuario->id]);
        
        // Actualizar datos del cliente
        $cliente->update([
            'documento_identidad' => $validated['documento_identidad'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
        ]);

        return redirect()->route('cliente.perfil')->with('success', 'Perfil actualizado exitosamente.');
    }
}
