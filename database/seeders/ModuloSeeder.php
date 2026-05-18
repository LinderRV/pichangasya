<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Modulo;

class ModuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $modulos = [
                [
                'nombre' => 'Complejos Deportivos',
                'codigo' => '1',
                ],
                [
                    'nombre' => 'Canchas',
                    'codigo' => '2',
                ]


                [
                    'nombre' => 'Gestion de Reservas',
                    'codigo' => '3',
                ],
                [

                    'nombre' => 'Gestion de Pagos',
                    'codigo' => '4',
                ],
                [
                    'nombre' => 'Gestion de Reportes',
                    'codigo' => '5',
                ],
                //SISTEMA
                [
                    'nombre' => 'Gestion roles y permisos',
                    'codigo' => '6',
                ]

            ];
    
            Modulo::insert($modulos);
    }
}
