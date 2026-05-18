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
    </style>
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <h1>¡Bienvenido, {{ Auth::user()->nombres }}!</h1>
                <p>Acceso al panel de control de PichangasYa</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total de Complejos</h3>
                    <div class="number">0</div>
                </div>
                <div class="stat-card">
                    <h3>Reservas Hoy</h3>
                    <div class="number">0</div>
                </div>
                <div class="stat-card">
                    <h3>Clientes Activos</h3>
                    <div class="number">0</div>
                </div>
                <div class="stat-card">
                    <h3>Ingresos del Mes</h3>
                    <div class="number">S/.0</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">Información del Usuario</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <td class="fw-bold">Nombre:</td>
                                <td>{{ Auth::user()->nombres }} {{ Auth::user()->apellidos }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Email:</td>
                                <td>{{ Auth::user()->email }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Teléfono:</td>
                                <td>{{ Auth::user()->telefono ?? 'No registrado' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Estado:</td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst(Auth::user()->estado) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
