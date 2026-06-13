<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reembolso extends Model
{
    protected $table = 'reembolsos';

    protected $fillable = [
        'id_reserva', 'id_pago', 'id_usuario',
        'monto', 'metodo_reembolso', 'codigo_operacion',
        'observacion', 'fecha_reembolso',
    ];

    protected $casts = ['fecha_reembolso' => 'datetime'];
}
