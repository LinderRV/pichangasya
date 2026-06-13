<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEstadoReserva extends Model
{
    protected $table = 'historial_estado_reservas';

    protected $fillable = [
        'id_reserva', 'id_estado_reserva', 'id_usuario',
        'fecha_cambio', 'observacion',
    ];

    protected $casts = ['fecha_cambio' => 'datetime'];
}
