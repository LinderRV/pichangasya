<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use  App\Models\ComplejoDeportivo;

class ComplejoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complejos = [
            [
                'id_distrito' => 1,   
                'nombre' => 'Complejo Deportivo Central',
                'descripcion' => 'Un complejo deportivo con canchas de fútbol y básquet.',
                'ruc' => '12345678901',
                'correo' => 'info@complejodeportivocentral.com',
                'direccion' => 'Av. Principal 123, Distrito Central',
                
            ],
        
        ];

        ComplejoDeportivo::insert($complejos);
        
    }
}
