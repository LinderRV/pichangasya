<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            UsuarioSeeder::class,
            UsuarioRolSeeder::class,
            TipoCanchaSeeder::class,
            MetodoPagoSeeder::class,
            EstadoReservaSeeder::class,
            DepartamentoSeeder::class,
            ProvinciaSeeder::class,
            DistritoSeeder::class,
            ComplejoSeeder::class,
            UsuarioComplejoSeeder::class,  
        ]);
        
    }
}
