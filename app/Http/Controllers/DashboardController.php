<?php

namespace App\Http\Controllers;

use App\Models\EstadoReserva;
use App\Models\HorarioConfigurado;
use App\Models\Pago;
use App\Models\Reserva;
use App\Models\UsuarioComplejo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        if ($usuario->esCliente()) {
            return redirect()->route('cliente.perfil');
        }

        $esSuperAdmin = $usuario->esSuperAdmin();
        $idComplejo   = $esSuperAdmin ? null : UsuarioComplejo::where('id_usuario', $usuario->id)->value('id_complejo');

        $reservasHoy = Reserva::whereDate('fecha_reserva', now()->toDateString())
            ->where('id_estado_reserva', '!=', EstadoReserva::CANCELADA)
            ->when(!$esSuperAdmin, fn($q) => $q->whereHas('cancha', fn($c) => $c->where('id_complejo', $idComplejo)))
            ->count();

        $ingresosMes = Pago::where('estado', 'confirmado')
            ->whereYear('fecha_pago', now()->year)
            ->whereMonth('fecha_pago', now()->month)
            ->when(!$esSuperAdmin, fn($q) => $q->whereHas('reserva.cancha', fn($c) => $c->where('id_complejo', $idComplejo)))
            ->sum('monto');

        $tasaOcupacionMes = $this->calcularTasaOcupacion($esSuperAdmin, $idComplejo);
        $rankingIngresos  = $this->calcularRankingIngresos($esSuperAdmin, $idComplejo);
        $rankingTitulo    = $esSuperAdmin ? 'Top Complejos por Ingresos (Mes)' : 'Top Canchas por Ingresos (Mes)';

        return view('admin.dashboard', compact(
            'reservasHoy', 'ingresosMes', 'tasaOcupacionMes', 'rankingIngresos', 'rankingTitulo'
        ));
    }

    private function calcularTasaOcupacion(bool $esSuperAdmin, ?int $idComplejo): float
    {
        $inicioMes = now()->copy()->startOfMonth();
        $hoy       = now();

        $diasSemanaMap = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        $ocurrenciasPorDia = [];
        foreach (CarbonPeriod::create($inicioMes, $hoy) as $dia) {
            $nombre = $diasSemanaMap[$dia->isoWeekday()];
            $ocurrenciasPorDia[$nombre] = ($ocurrenciasPorDia[$nombre] ?? 0) + 1;
        }

        $horarios = HorarioConfigurado::where('estado', 'activo')
            ->when(!$esSuperAdmin, fn($q) => $q->whereHas('cancha', fn($c) => $c->where('id_complejo', $idComplejo)))
            ->get(['dia_semana', 'hora_inicio', 'hora_fin']);

        $horasDisponibles = 0;
        foreach ($horarios as $horario) {
            $horas = Carbon::parse($horario->hora_inicio)->diffInMinutes(Carbon::parse($horario->hora_fin)) / 60;
            $horasDisponibles += $horas * ($ocurrenciasPorDia[$horario->dia_semana] ?? 0);
        }

        if ($horasDisponibles <= 0) {
            return 0;
        }

        $horasReservadas = Reserva::whereBetween('fecha_reserva', [$inicioMes->toDateString(), $hoy->toDateString()])
            ->where('id_estado_reserva', '!=', EstadoReserva::CANCELADA)
            ->when(!$esSuperAdmin, fn($q) => $q->whereHas('cancha', fn($c) => $c->where('id_complejo', $idComplejo)))
            ->get(['hora_inicio', 'hora_fin'])
            ->sum(fn($r) => Carbon::parse($r->hora_inicio)->diffInMinutes(Carbon::parse($r->hora_fin)) / 60);

        return round(min($horasReservadas / $horasDisponibles * 100, 100), 1);
    }

    private function calcularRankingIngresos(bool $esSuperAdmin, ?int $idComplejo)
    {
        $query = Pago::where('pagos.estado', 'confirmado')
            ->whereYear('pagos.fecha_pago', now()->year)
            ->whereMonth('pagos.fecha_pago', now()->month)
            ->join('reservas', 'reservas.id', '=', 'pagos.id_reserva')
            ->join('canchas', 'canchas.id', '=', 'reservas.id_cancha');

        if ($esSuperAdmin) {
            $query->join('complejo_deportivos', 'complejo_deportivos.id', '=', 'canchas.id_complejo')
                ->select('complejo_deportivos.nombre as etiqueta', DB::raw('SUM(pagos.monto) as total'))
                ->groupBy('complejo_deportivos.id', 'complejo_deportivos.nombre');
        } else {
            $query->where('canchas.id_complejo', $idComplejo)
                ->select('canchas.nombre as etiqueta', DB::raw('SUM(pagos.monto) as total'))
                ->groupBy('canchas.id', 'canchas.nombre');
        }

        return $query->orderByDesc('total')->limit(5)->get();
    }
}
