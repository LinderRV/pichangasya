<?php

namespace App\Http\Controllers\Admin\Rol;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index()
    {
        return view('admin.rol.index');
    }
    
}
