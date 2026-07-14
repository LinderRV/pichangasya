@extends('layouts.app')

@section('link')
    <style nonce="{{ request()->attributes->get('csp_nonce') }}">
        .welcome-card {
            background: linear-gradient(135deg, #198754 0%, #145c35 100%);
            border-radius: 18px;
            padding: 30px;
            color: white;
            box-shadow: 0 10px 30px rgba(25, 135, 84, 0.2);
        }
        .welcome-card h1 {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 10px;
        }
        .welcome-card p {
            font-size: 1rem;
            opacity: 0.95;
            margin: 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #198754;
        }
        .stat-card h3 {
            font-size: 0.9rem;
            color: #666;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .stat-card .number {
            font-size: 1.8rem;
            font-weight: 900;
            color: #198754;
        }
        .stat-card .meta { color:#6b7280; font-size:.8rem; margin-top:.35rem; }
        .stat-card.warning { border-left-color:#f59e0b; }
        .stat-card.warning .number { color:#b45309; }
        .stat-card.danger { border-left-color:#dc3545; }
        .stat-card.danger .number { color:#dc3545; }
        .metric-up { color:#198754; font-weight:700; }
        .metric-down { color:#dc3545; font-weight:700; }
        .ranking-card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }
        .ranking-card h3 {
            font-size: 1rem;
            color: #333;
            margin: 0 0 15px 0;
            font-weight: 700;
        }
        .ranking-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .ranking-item:last-child {
            border-bottom: none;
        }
        .ranking-item .etiqueta {
            color: #444;
            font-weight: 600;
        }
        .ranking-item .monto {
            color: #198754;
            font-weight: 700;
        }
        .ranking-empty {
            color: #999;
            font-size: 0.9rem;
            margin: 0;
        }
        .dashboard-panels { display:grid; grid-template-columns:minmax(0,1.6fr) minmax(280px,.8fr); gap:20px; margin-top:20px; }
        .trend-row { display:grid; grid-template-columns:65px minmax(120px,1fr) 105px 90px; gap:12px; align-items:center; padding:10px 0; border-bottom:1px solid #f0f0f0; }
        .trend-row:last-child { border-bottom:0; }
        .trend-track { height:10px; background:#eef2f7; border-radius:999px; overflow:hidden; }
        .trend-bar { height:100%; background:linear-gradient(90deg,#198754,#52b788); border-radius:999px; min-width:2px; }
        .alert-link { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px; border-radius:12px; background:#fff8e6; color:#7c5700; text-decoration:none; }
        .alert-link:hover { color:#5c4100; }
        @media(max-width:991.98px){ .dashboard-panels{ grid-template-columns:1fr; } }
        @media(max-width:575.98px){ .trend-row{ grid-template-columns:55px 1fr 80px; } .trend-reservas{ display:none; } }
    </style>
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <h1>Bienvenido, {{ Auth::user()->nombres }}</h1>
                <p>Acceso al panel de control de PichangasYa</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Reservas Hoy</h3>
                    <div class="number">{{ $reservasHoy }}</div>
                    <div class="meta">{{ $proximasHoy }} aún por atender</div>
                </div>
                <div class="stat-card">
                    <h3>Ingresos del Mes</h3>
                    <div class="number">S/. {{ number_format($ingresosMes, 2) }}</div>
                    <div class="meta">
                        @if($variacionIngresos === null)
                            Sin base de comparación el mes anterior
                        @else
                            <span class="{{ $variacionIngresos >= 0 ? 'metric-up' : 'metric-down' }}">{{ $variacionIngresos >= 0 ? '+' : '' }}{{ $variacionIngresos }}%</span> frente al mes anterior
                        @endif
                    </div>
                </div>
                <div class="stat-card">
                    <h3>Tasa de Ocupación (Mes)</h3>
                    <div class="number">{{ $tasaOcupacionMes }}%</div>
                    <div class="meta">Horas reservadas sobre horas configuradas</div>
                </div>
                <div class="stat-card">
                    <h3>Ticket Promedio</h3>
                    <div class="number">S/. {{ number_format($ticketPromedioMes, 2) }}</div>
                    <div class="meta">Promedio de pagos confirmados del mes</div>
                </div>
                <div class="stat-card">
                    <h3>Reservas del Mes</h3>
                    <div class="number">{{ $reservasMes }}</div>
                    <div class="meta">No incluye reservas canceladas</div>
                </div>
                <div class="stat-card {{ $tasaCancelacionMes > 15 ? 'danger' : '' }}">
                    <h3>Tasa de Cancelación</h3>
                    <div class="number">{{ $tasaCancelacionMes }}%</div>
                    <div class="meta">{{ $canceladasMes }} cancelada(s) durante el mes</div>
                </div>
                <div class="stat-card warning">
                    <h3>Reembolsos del Mes</h3>
                    <div class="number">S/. {{ number_format($reembolsosMes, 2) }}</div>
                    <div class="meta">Monto registrado como devuelto</div>
                </div>
                <div class="stat-card {{ $canchasSinHorario > 0 ? 'warning' : '' }}">
                    <h3>Canchas sin Horario Activo</h3>
                    <div class="number">{{ $canchasSinHorario }}</div>
                    <div class="meta">No aparecerán con horarios reservables</div>
                </div>
            </div>

            <div class="dashboard-panels">
                <div class="ranking-card mt-0">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <h3 class="mb-0">Evolución de los últimos 6 meses</h3>
                        <span class="text-muted small">Ingresos y reservas</span>
                    </div>
                    @php($maxIngresos = max(1, (float) $tendenciaMensual->max('ingresos')))
                    @foreach($tendenciaMensual as $periodo)
                        <div class="trend-row">
                            <strong class="small">{{ $periodo['periodo'] }}</strong>
                            <div class="trend-track" aria-label="Ingreso S/. {{ number_format($periodo['ingresos'], 2) }}">
                                <div class="trend-bar" style="width: {{ ($periodo['ingresos'] / $maxIngresos) * 100 }}%"></div>
                            </div>
                            <strong class="text-success text-end">S/. {{ number_format($periodo['ingresos'], 0) }}</strong>
                            <span class="text-muted small text-end trend-reservas">{{ $periodo['reservas'] }} reservas</span>
                        </div>
                    @endforeach
                </div>

                <div class="d-grid gap-3">
                    <div class="ranking-card mt-0">
                        <h3>{{ $rankingTitulo }}</h3>
                        @forelse ($rankingIngresos as $item)
                            <div class="ranking-item">
                                <span class="etiqueta">{{ $item->etiqueta }}</span>
                                <span class="monto">S/. {{ number_format($item->total, 2) }}</span>
                            </div>
                        @empty
                            <p class="ranking-empty">Aún no hay ingresos registrados este mes.</p>
                        @endforelse
                    </div>

                    @if($canchasSinHorario > 0)
                    <div class="ranking-card mt-0">
                        <h3>Acción recomendada</h3>
                        <a class="alert-link" href="{{ route('admin.horarios.index') }}">
                            <span><i class="bi bi-exclamation-triangle me-2"></i>Configura horarios para {{ $canchasSinHorario }} cancha(s)</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
