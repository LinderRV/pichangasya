<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEstadoReserva extends Model
{
    protected $table = 'historial_estado_reservas';

    protected $fillable = [
        'id_reserva', 'id_estado_reserva', 'id_usuario',
        'fecha_cambio', 'observacion',
    ];

    protected $casts = ['fecha_cambio' => 'datetime'];

    public function estadoReserva(): BelongsTo
    {
        return $this->belongsTo(EstadoReserva::class, 'id_estado_reserva');
    }
}
