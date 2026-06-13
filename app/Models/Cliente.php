<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'id_usuario',
        'documento_identidad',
        'direccion',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_cliente');
    }
}
