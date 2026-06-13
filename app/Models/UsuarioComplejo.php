<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuarioComplejo extends Model
{
    protected $table = 'usuario_complejos';

    protected $fillable = [
        'id_usuario',
        'id_complejo',
        'cargo',
        'estado',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function complejo(): BelongsTo
    {
        return $this->belongsTo(ComplejoDeportivo::class, 'id_complejo');
    }
}
