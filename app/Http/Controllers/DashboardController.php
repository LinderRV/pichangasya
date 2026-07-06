<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EstadoReserva;
use App\Models\Pago;
use App\Models\Reserva;
use App\Models\UsuarioComplejo;
use Illuminate\Support\Facades\Auth;

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

        $nuevosClientesMes = Cliente::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->when(!$esSuperAdmin, fn($q) => $q->whereHas('reservas.cancha', fn($c) => $c->where('id_complejo', $idComplejo)))
            ->count();

        $reservasDelMesQuery = fn() => Reserva::whereYear('fecha_reserva', now()->year)
            ->whereMonth('fecha_reserva', now()->month)
            ->when(!$esSuperAdmin, fn($q) => $q->whereHas('cancha', fn($c) => $c->where('id_complejo', $idComplejo)));

        $totalReservasMes = $reservasDelMesQuery()->count();
        $canceladasMes    = $reservasDelMesQuery()->where('id_estado_reserva', EstadoReserva::CANCELADA)->count();
        $tasaCancelacionMes = $totalReservasMes > 0 ? round($canceladasMes / $totalReservasMes * 100, 1) : 0;

        return view('admin.dashboard', compact('reservasHoy', 'ingresosMes', 'nuevosClientesMes', 'tasaCancelacionMes'));
    }
}
