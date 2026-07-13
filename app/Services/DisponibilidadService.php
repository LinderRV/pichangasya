<?php

namespace App\Services;

use App\Models\Cancha;
use App\Models\HorarioConfigurado;
use App\Models\BloqueoCancha;
use App\Models\Reserva;
use App\Models\EstadoReserva;

class DisponibilidadService
{
    /**
     * Duraciones de reserva permitidas, en minutos.
     */
    public const DURACIONES_PERMITIDAS = [60, 90, 120, 150, 180];

    public static function duracionValida(int $minutos): bool
    {
        return in_array($minutos, self::DURACIONES_PERMITIDAS, true);
    }

    /**
     * Horarios de inicio disponibles para una duración de reserva dada.
     * El "intervalo_minutos" de horario_configurados define cada cuánto
     * puede empezar una reserva (paso), no la duración de la misma.
     */
    public function slotsDisponibles(Cancha $cancha, string $fecha, int $duracionMinutos = 60, ?int $excluirReservaId = null): array
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
            ->when($excluirReservaId, fn($q) => $q->where('id', '!=', $excluirReservaId))
            ->get();

        $slots = [];
        $duracionHoras = $duracionMinutos / 60;
        $total = round($cancha->precio_hora * $duracionHoras, 2);

        foreach ($horarios as $horario) {
            $inicioMin  = $this->aMinutos(substr($horario->hora_inicio, 0, 5));
            $finMin     = $this->aMinutos(substr($horario->hora_fin, 0, 5));
            $paso       = (int) $horario->intervalo_minutos;
            $slotInicio = $inicioMin;

            while ($slotInicio + $duracionMinutos <= $finMin) {
                $slotFin = $slotInicio + $duracionMinutos;

                if (!$this->estaBloquedado($slotInicio, $slotFin, $bloqueos) &&
                    !$this->estaReservado($slotInicio, $slotFin, $reservasOcupadas)) {

                    $slots[] = [
                        'hora_inicio' => $this->desdeMinutos($slotInicio),
                        'hora_fin'    => $this->desdeMinutos($slotFin),
                        'precio_hora' => (float) $cancha->precio_hora,
                        'total'       => $total,
                    ];
                }

                $slotInicio += $paso;
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
