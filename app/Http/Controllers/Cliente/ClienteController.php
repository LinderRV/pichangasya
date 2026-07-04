<?php

namespace App\Http\Controllers\Cliente;

use App\Helpers\Service;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class ClienteController extends Controller
{
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
     * Mostrar las reservas del cliente (solo lectura)
     */
    public function reservas()
    {
        $usuario = Auth::user();

        return view('cliente.reservas', compact('usuario'));
    }

  
    public function actualizarPerfil(Request $request)
    {
        try {
            $usuario = Auth::user();
            $clienteId = $usuario->cliente ? $usuario->cliente->id : null;

            $validated = $request->validate([
                'nombres' => ['required', 'string', 'max:100'],
                'apellidos' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:200', 'unique:usuarios,email,' . $usuario->id],
                'telefono' => ['nullable', 'string', 'max:20'],
                'sexo' => ['nullable', 'in:masculino,femenino'],
                'documento_identidad' => ['required', 'string', 'max:20', 'unique:clientes,documento_identidad,' . $clienteId],
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
            $cliente = $usuario->cliente;
            if ($cliente) {
                $cliente->update([
                    'documento_identidad' => $validated['documento_identidad'],
                    'direccion' => $validated['direccion'] ?? null,
                ]);
            } else {
                Cliente::create([
                    'id_usuario' => $usuario->id,
                    'documento_identidad' => $validated['documento_identidad'],
                    'direccion' => $validated['direccion'] ?? null,
                ]);
            }

            return response()->json(Service::responseSuccess('Perfil actualizado exitosamente.'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }
}
