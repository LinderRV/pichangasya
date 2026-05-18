<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoCancha;

class TipoCanchaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposCancha = [
            [
                'nombre' => 'Fútbol 5',
                'descripcion' => 'Cancha de fútbol para equipos de 5 jugadores, ideal para partidos rápidos y dinámicos.',
            ],
            [
                'nombre' => 'Fútbol 7',
                'descripcion' => 'Cancha de fútbol para equipos de 7 jugadores, perfecta para partidos más estratégicos y con mayor espacio.',
            ],
            [
                'nombre' => 'Fútbol 11',
                'descripcion' => 'Cancha de fútbol tradicional para equipos de 11 jugadores, ideal para partidos completos y torneos.',
            ],
            [
                'nombre' => 'Vóley',
                'descripcion' => 'Cancha de vóley para partidos recreativos.',
            ],
            [
                'nombre' => 'Básquet',
                'descripcion' => 'Cancha de básquet para partidos recreativos.',
            ],
            
        ];

        TipoCancha::insert($tiposCancha);    
        

    }
}
