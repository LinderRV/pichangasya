<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioComplejoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarioComplejo = [
            [
                'id_usuario' => 1, 
                'id_complejo' => 1, 
                'cargo' => 'Administrador',
            ],
            [
                'id_usuario' => 2, 
                'id_complejo' => 1, 
                'cargo' => 'Empleado',
            ],
            [
                'id_usuario' => 3, 
                'id_complejo' => 1, 
                'cargo' => 'Empleado',
            ],
        ];

        DB::table('usuario_complejos')->insert($usuarioComplejo);
    }
}
