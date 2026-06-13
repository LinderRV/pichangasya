<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombres',
        'apellidos',
        'email',
        'password',
        'estado',
        'telefono',
        'sexo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_acceso_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relación con Cliente (un usuario puede tener un cliente)
     */
    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'id_usuario');
    }

    /**
     * Roles del usuario (tabla pivote usuario_rol)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol', 'id_usuario', 'id_rol');
    }

    /**
     * Indica si el usuario tiene el rol Super Admin (id_rol = 1)
     */
    public function esSuperAdmin(): bool
    {
        return $this->roles()->where('roles.id', 1)->exists();
    }

    /**
     * Indica si el usuario tiene el rol Usuario Interno / Dueño (id_rol = 2)
     */
    public function esUsuarioInterno(): bool
    {
        return $this->roles()->where('roles.id', 2)->exists();
    }

    /**
     * Indica si el usuario tiene el rol Cliente (id_rol = 3)
     */
    public function esCliente(): bool
    {
        return $this->roles()->where('roles.id', 3)->exists();
    }
}
