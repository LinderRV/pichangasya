<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'codigo_reserva',
        'id_cliente',
        'id_cancha',
        'id_estado_reserva',
        'fecha_reserva',
        'hora_inicio',
        'hora_fin',
        'precio_hora',
        'subtotal',
        'total',
        'confirmado_at',
        'cancelado_at',
        'id_usuario_cancelado',
        'motivo_cancelacion',
    ];

    protected $casts = [
        'confirmado_at' => 'datetime',
        'cancelado_at'  => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function cancha(): BelongsTo
    {
        return $this->belongsTo(Cancha::class, 'id_cancha');
    }

    public function estadoReserva(): BelongsTo
    {
        return $this->belongsTo(EstadoReserva::class, 'id_estado_reserva');
    }

    public function usuarioCancelado(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_cancelado');
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class, 'id_reserva');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(HistorialEstadoReserva::class, 'id_reserva')->orderBy('fecha_cambio');
    }

    public function reembolso(): HasOne
    {
        return $this->hasOne(Reembolso::class, 'id_reserva');
    }
}
