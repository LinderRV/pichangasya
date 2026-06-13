<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoReserva extends Model
{
    protected $table = 'estado_reservas';
    protected $fillable = ['nombre', 'descripcion'];

    const CONFIRMADA  = 1;
    const COMPLETADA  = 2;
    const CANCELADA   = 3;
}
