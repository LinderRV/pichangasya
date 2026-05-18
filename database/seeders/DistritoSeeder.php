<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Distrito;

class DistritoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distritos = [
            [
                'nombre' => 'San Juan de Lurigancho',
                'id_provincia' => 1,
                ],
            ];

        Distrito::insert($distritos);
        
    }
}
