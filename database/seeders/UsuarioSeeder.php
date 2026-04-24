<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'rol_id' => 1,
                'nombres' => 'Super Admin', // Super Admin
                'apellidos' => 'PichangasYa',
                'correo' => 'superadmin@pichangasya.com',
                'contrasena' => Hash::make('PichangasYa2026*'),
                'telefono' => '900000001',
            ],
            [
                'rol_id' => 2,
                'nombres' => 'Administrador',
                'apellidos' => 'General',
                'correo' => 'administrador@pichangasya.com',
                'contrasena' => Hash::make('PichangasYa2026*'),
                'telefono' => '900000002',
            ],
            [
                'rol_id' => 3,
                'nombres' => 'Administrador',
                'apellidos' => 'Complejo',
                'correo' => 'complejo.admin@pichangasya.com',
                'contrasena' => Hash::make('PichangasYa2026*'),
                'telefono' => '900000003',
            ],
            [
                'rol_id' => 4,
                'nombres' => 'Empleado',
                'apellidos' => 'Operativo',
                'correo' => 'empleado@pichangasya.com',
                'contrasena' => Hash::make('PichangasYa2026*'),
                'telefono' => '900000004',
            ],
            [
                'rol_id' => 5,
                'nombres' => 'Soporte',
                'apellidos' => 'Sistema',
                'correo' => 'soporte@pichangasya.com',
                'contrasena' => Hash::make('PichangasYa2026*'),
                'telefono' => '900000005',
            ],
            [
                'rol_id' => 6,
                'nombres' => 'Cliente',
                'apellidos' => 'Prueba',
                'correo' => 'cliente@pichangasya.com',
                'contrasena' => Hash::make('PichangasYa2026*'),
                'telefono' => '900000006',
            ],
            
        ];

        Usuario::insert($usuarios);
    }
}
