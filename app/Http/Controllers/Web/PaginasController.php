<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaginasController extends Controller
{
    public function inicio()
    {
    return view('web.paginas.Inicio');
    }
}
