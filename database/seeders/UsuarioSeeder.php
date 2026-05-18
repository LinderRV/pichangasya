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
                'nombres' => 'Linder', 
                'apellidos' => 'Revilla',
                'email' => 'superadmin@pichangasya.com',
                'password' => Hash::make('PichangasYa2026*'),
                'telefono' => '900000001',
                'sexo' => 'masculino',
            ],
            [
                'nombres' => 'Juan', 
                'apellidos' => 'Velasquez',
                'email' => 'juan.velasquez@gmail.com',
                'password' => Hash::make('PichangasYa2026*'),
                'telefono' => '900000003',
                'sexo' => 'masculino',
            ],
            [
                'nombres' => 'Luis', 
                'apellidos' => 'Gomez',
                'email' => 'luis.gomez@gmail.com',
                'password' => Hash::make('PichangasYa2026*'),
                'telefono' => '900000004',
                'sexo' => 'masculino',
            ],
            
        ];

        Usuario::insert($usuarios);
    }
}
