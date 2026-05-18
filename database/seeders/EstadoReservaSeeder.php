<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstadoReserva;

class EstadoReservaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //NO EXISTE EL ESTADO PENDIENTE PORQUE SOLO SE REGISTRAN LAS RESERVAS CUANDO EL PAGO ES CONFIRMADO, POR LO TANTO, LA RESERVA SE CREA DIRECTAMENTE CON EL ESTADO CONFIRMADA
        $estados = [
            [
                'nombre' => 'Confirmada',
                'descripcion' => 'La reserva se registra únicamente cuando el pago online fue confirmado. Es el estado principal de una reserva válida.',
            ],
            [
                'nombre' => 'Completada',
                'descripcion' => 'La reserva ya fue utilizada',
            ],                        
            [
                'nombre' => 'Cancelada',
                'descripcion' => 'Reserva cancelada por el dueño, empleado o superadministrador tras evaluar el caso.',
            ],

        ];

       EstadoReserva::insert($estados);
        
    }
}
