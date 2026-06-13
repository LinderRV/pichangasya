<?php

namespace App\Services;

use App\Models\Cancha;
use App\Models\HorarioConfigurado;
use App\Models\BloqueoCancha;
use App\Models\Reserva;
use App\Models\EstadoReserva;

class DisponibilidadService
{
    public function slotsDisponibles(Cancha $cancha, string $fecha): array
    {
        $diaSemana = $this->diaSemanaEspanol(date('N', strtotime($fecha)));

        $horarios = HorarioConfigurado::where('id_cancha', $cancha->id)
            ->where('dia_semana', $diaSemana)
            ->where('estado', 'activo')
            ->get();

        if ($horarios->isEmpty()) return [];

        $bloqueos = BloqueoCancha::where('id_cancha', $cancha->id)
            ->where('fecha', $fecha)
            ->get();

        $reservasOcupadas = Reserva::where('id_cancha', $cancha->id)
            ->where('fecha_reserva', $fecha)
            ->where('id_estado_reserva', '!=', EstadoReserva::CANCELADA)
            ->get();

        $slots = [];

        foreach ($horarios as $horario) {
            $inicioMin   = $this->aMinutos(substr($horario->hora_inicio, 0, 5));
            $finMin      = $this->aMinutos(substr($horario->hora_fin, 0, 5));
            $intervalo   = (int) $horario->intervalo_minutos;
            $slotInicio  = $inicioMin;

            while ($slotInicio + $intervalo <= $finMin) {
                $slotFin = $slotInicio + $intervalo;

                if (!$this->estaBloquedado($slotInicio, $slotFin, $bloqueos) &&
                    !$this->estaReservado($slotInicio, $slotFin, $reservasOcupadas)) {

                    $duracionHoras = $intervalo / 60;
                    $total = round($cancha->precio_hora * $duracionHoras, 2);

                    $slots[] = [
                        'hora_inicio' => $this->desdeMinutos($slotInicio),
                        'hora_fin'    => $this->desdeMinutos($slotFin),
                        'precio_hora' => (float) $cancha->precio_hora,
                        'total'       => $total,
                    ];
                }

                $slotInicio = $slotFin;
            }
        }

        return $slots;
    }

    private function diaSemanaEspanol(int $iso): string
    {
        return ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'][$iso - 1];
    }

    private function aMinutos(string $hora): int
    {
        [$h, $m] = explode(':', $hora);
        return (int)$h * 60 + (int)$m;
    }

    private function desdeMinutos(int $minutos): string
    {
        return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
    }

    private function estaBloquedado(int $inicio, int $fin, $bloqueos): bool
    {
        foreach ($bloqueos as $b) {
            $bInicio = $this->aMinutos(substr($b->hora_inicio, 0, 5));
            $bFin    = $this->aMinutos(substr($b->hora_fin, 0, 5));
            if ($inicio < $bFin && $fin > $bInicio) return true;
        }
        return false;
    }

    private function estaReservado(int $inicio, int $fin, $reservas): bool
    {
        foreach ($reservas as $r) {
            $rInicio = $this->aMinutos(substr($r->hora_inicio, 0, 5));
            $rFin    = $this->aMinutos(substr($r->hora_fin, 0, 5));
            if ($inicio < $rFin && $fin > $rInicio) return true;
        }
        return false;
    }
}
