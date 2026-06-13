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
        // Solo se asigna el Dueño al complejo de ejemplo.
        // Los demás usuarios internos (4, 5) quedan disponibles para que
        // el Super Admin los asigne como Dueño/Empleado desde el módulo.
        $usuarioComplejo = [
            [
                'id_usuario' => 2,
                'id_complejo' => 1,
                'cargo' => 'Dueño',
            ],
        ];

        DB::table('usuario_complejos')->insert($usuarioComplejo);
    }
}
