<?php

namespace App\Http\Controllers\Admin\Pago;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Service;
use App\Models\MetodoPago;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class MetodoPagoController extends Controller
{
    public function index()
    {
        return view('admin.pago.metodo.index');
    }

    public function lista()
    {
        try {
            $metodos = MetodoPago::select('id', 'nombre', 'estado')->get();

            return response()->json(Service::responseSuccess('OK', $metodos));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function obtener($id)
    {
        try {
            $metodo = MetodoPago::find($id);
            if (!$metodo) {
                return response()->json(Service::responseError('Método de pago no encontrado'), 404);
            }

            return response()->json(Service::responseSuccess('OK', $metodo));
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
                'nombre' => ['required', 'string', 'max:80', 'unique:metodo_pagos,nombre'],
                'estado' => ['required', 'in:activo,inactivo'],
            ]);

            $metodo = MetodoPago::create($request->only(['nombre', 'estado']));

            return response()->json(Service::responseSuccess('Método de pago registrado correctamente', $metodo));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $metodo = MetodoPago::find($id);
            if (!$metodo) {
                return response()->json(Service::responseError('Método de pago no encontrado'), 404);
            }

            $request->validate([
                'nombre' => ['required', 'string', 'max:80', 'unique:metodo_pagos,nombre,' . $id],
                'estado' => ['required', 'in:activo,inactivo'],
            ]);

            $metodo->update($request->only(['nombre', 'estado']));

            return response()->json(Service::responseSuccess('Método de pago actualizado correctamente', $metodo));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }

    public function eliminar($id)
    {
        try {
            $metodo = MetodoPago::find($id);
            if (!$metodo) {
                return response()->json(Service::responseError('Método de pago no encontrado'), 404);
            }

            $enUso = DB::table('pagos')->where('id_metodo_pago', $id)->exists();
            if ($enUso) {
                return response()->json(Service::responseError('No se puede eliminar: el método de pago tiene pagos registrados.'));
            }

            $metodo->delete();

            return response()->json(Service::responseSuccess('Método de pago eliminado correctamente'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
    }
}
