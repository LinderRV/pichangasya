<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ComplejoDeportivo extends Model
{
    protected $table = 'complejo_deportivos';

    protected $fillable = [
        'id_distrito',
        'nombre',
        'descripcion',
        'ruc',
        'correo',
        'direccion',
        'telefono',
        'imagen',
        'estado',
    ];

    public function distrito(): BelongsTo
    {
        return $this->belongsTo(Distrito::class, 'id_distrito');
    }

    public function usuarioComplejo(): HasOne
    {
        return $this->hasOne(UsuarioComplejo::class, 'id_complejo');
    }
}
