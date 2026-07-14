<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reembolso extends Model
{
    protected $table = 'reembolsos';

    protected $fillable = [
        'id_reserva', 'id_pago', 'id_usuario',
        'monto', 'metodo_reembolso', 'codigo_operacion',
        'observacion', 'fecha_reembolso',
    ];

    protected $casts = ['fecha_reembolso' => 'datetime'];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'id_reserva');
    }
}
