@extends('layouts.app')

@section('link')
    <style>
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            font-size: 2rem;
            font-weight: 900;
            color: #198754;
        }
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
                </div>
                <div class="stat-card">
                    <h3>Ingresos del Mes</h3>
                    <div class="number">S/. {{ number_format($ingresosMes, 2) }}</div>
                </div>
                <div class="stat-card">
                    <h3>Tasa de Ocupación (Mes)</h3>
                    <div class="number">{{ $tasaOcupacionMes }}%</div>
                </div>
            </div>

            <div class="ranking-card">
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
        </div>
    </div>
@endsection
