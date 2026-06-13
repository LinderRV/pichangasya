<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'id_reserva',
        'id_metodo_pago',
        'codigo_operacion',
        'monto',
        'comprobante_url',
        'estado',
        'fecha_pago',
    ];

    protected $casts = ['fecha_pago' => 'datetime'];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'id_reserva');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago');
    }
}
