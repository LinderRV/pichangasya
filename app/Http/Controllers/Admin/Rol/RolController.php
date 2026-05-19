<?php

namespace App\Http\Controllers\Admin\Rol;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Util;
use App\Helpers\Service;
use App\Models\Rol;
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
            $roles = Rol::select('id', 'nombre', 'descripcion')
        ->get()
        ->map(function ($rol) {
            return [
                'id' => $rol->id,
                'nombre' => $rol->nombre,
                'descripcion' => $rol->descripcion,
            ];
        });

        return response()->json(Service::responseSuccess('Roles obtenidos correctamente', $roles));
        } catch (Exception $e) {
            return response()->json(Service::responseError('Error Servidor ' . $e->getMessage()));
        }
        
        
    }



    
}
