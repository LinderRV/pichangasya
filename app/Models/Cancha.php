<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cancha extends Model
{
    protected $table = 'canchas';

    protected $fillable = [
        'id_complejo',
        'id_tipo_cancha',
        'nombre',
        'descripcion',
        'precio_hora',
        'capacidad',
        'foto',
        'estado',
    ];

    public function complejo(): BelongsTo
    {
        return $this->belongsTo(ComplejoDeportivo::class, 'id_complejo');
    }

    public function tipoCancha(): BelongsTo
    {
        return $this->belongsTo(TipoCancha::class, 'id_tipo_cancha');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(HorarioConfigurado::class, 'id_cancha');
    }
}
