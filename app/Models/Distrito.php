<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distrito extends Model
{
    protected $table = 'distritos';

    protected $fillable = [
        'id_provincia',
        'nombre',
    ];

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class, 'id_provincia');
    }
}
