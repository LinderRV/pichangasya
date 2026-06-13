<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCancha extends Model
{
    protected $table = 'tipo_canchas';

    protected $fillable = ['nombre', 'descripcion'];
}
