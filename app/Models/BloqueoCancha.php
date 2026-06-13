<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloqueoCancha extends Model
{
    protected $table = 'bloqueo_canchas';

    protected $fillable = [
        'id_cancha',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'motivo',
        'descripcion',
    ];

    public function cancha(): BelongsTo
    {
        return $this->belongsTo(Cancha::class, 'id_cancha');
    }
}
