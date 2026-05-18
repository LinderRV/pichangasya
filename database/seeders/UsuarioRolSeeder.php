<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioRolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $usuarioRol = [
                // Super Admin
                [
                    'id_usuario' => 1, // ID del Super Admin
                    'id_rol' => 1, // ID del rol Super Admin
                ],

                [
                    'id_usuario' => 2, 
                    'id_rol' => 2, 
                ],
             
                [
                    'id_usuario' => 3, 
                    'id_rol' => 3, 
                ],
            ];
    
            DB::table('usuario_rol')->insert($usuarioRol);
        
    }
}
