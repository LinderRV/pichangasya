<?php

namespace App\Console\Commands;

use App\Models\EstadoReserva;
use App\Models\HistorialEstadoReserva;
use App\Models\Reserva;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CompletarReservasVencidas extends Command
{
    protected $signature = 'reservas:completar';
    protected $description = 'Marca como Completada toda reserva Confirmada cuyo horario ya finalizó';

    public function handle(): int
    {
        $hoy  = now()->toDateString();
        $hora = now()->toTimeString();

        Reserva::with('cliente')
            ->where('id_estado_reserva', EstadoReserva::CONFIRMADA)
            ->where(function ($q) use ($hoy, $hora) {
                $q->where('fecha_reserva', '<', $hoy)
                  ->orWhere(function ($q2) use ($hoy, $hora) {
                      $q2->where('fecha_reserva', $hoy)->where('hora_fin', '<=', $hora);
                  });
            })
            ->chunkById(200, function ($reservas) {
                foreach ($reservas as $reserva) {
                    DB::transaction(function () use ($reserva) {
                        $reserva->update(['id_estado_reserva' => EstadoReserva::COMPLETADA]);

                        HistorialEstadoReserva::create([
                            'id_reserva'        => $reserva->id,
                            'id_estado_reserva' => EstadoReserva::COMPLETADA,
                            'id_usuario'        => optional($reserva->cliente)->id_usuario,
                            'fecha_cambio'      => now(),
                            'observacion'       => 'Completada automáticamente: el horario de la reserva ya finalizó.',
                        ]);
                    });
                }
            });

        return self::SUCCESS;
    }
}
