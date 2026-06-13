<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioConfigurado extends Model
{
    protected $table = 'horario_configurados';

    protected $fillable = [
        'id_cancha',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'intervalo_minutos',
        'estado',
    ];

    public function cancha(): BelongsTo
    {
        return $this->belongsTo(Cancha::class, 'id_cancha');
    }
}
