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
        $roles = [
            [
                'nombre' => 'Super Admin',
                'descripcion' => 'Acceso total a todas las funcionalidades'
            ],
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Gestiona roles, usuarios, reservas, pagos y reportes, pero no tiene acceso a la configuración del sistema'
            ],
            [
                'nombre' => 'Complejos Admin',
                'descripcion' => 'Dueños de complejos deportivos, pueden gestionar sus canchas, reservas y pagos'
            ],
            [
                'nombre' => 'Empleados', // Empleados de los complejos deportivos
                'descripcion' => 'Empleados de los complejos deportivos, pueden gestionar reservas y pagos, pero no tienen acceso a la configuración del complejo'
            ],
                [
                    'nombre' => 'Soporte',
                    'descripcion' => 'Atiende incidencias, revisa reservas, pagos y problemas reportados sin acceso total al sistema'
            ],
            [
                'nombre' => 'Cliente',
                'descripcion' => 'Reserva canchas, realiza pagos y visualiza sus reservas, historial de pagos'
            ]

        ];

            Rol::insert($roles);
    }
}
