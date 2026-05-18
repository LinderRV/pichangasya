<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rol = [
            [
                'nombre' => 'Super Admin',// Super Admin
                'descripcion' => 'Control total del sistema - Acceso total a todas las funcionalidades'
            ],
            [
                'nombre' => 'Usuario Interno', 
                'descripcion' => 'Usuario asociado a uno o varios complejos deportivos.'
            ],
            [
                'nombre' => 'Cliente', 
                'descripcion' => 'Usuario final que reserva canchas deportivas.'
            ]
             

        ];

            Rol::insert($rol);
    }
}
