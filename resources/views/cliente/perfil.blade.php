@extends('layouts.app')
@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h4 class="card-title mb-0">Mi Perfil</h4>
                <p class="text-muted mb-0 mt-2">Completa y actualiza tu información personal</p>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (!$cliente->documento_identidad && !$cliente->direccion)
                    <div class="alert alert-primary border-primary" role="alert">
                        <div class="d-flex gap-2">
                            <i class="bi bi-lightbulb flex-shrink-0 mt-1" style="font-size: 1.2rem;"></i>
                            <div>
                                <strong>¡Bienvenido a PichangasYa!</strong>
                                <p class="mb-0 mt-1">Te invitamos a completar tu información de perfil para una mejor experiencia.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('cliente.actualizar') }}" method="POST" novalidate>
                    @csrf

                    <!-- Datos Personales -->
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="bi bi-person me-2"></i>Datos Personales</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombres" class="form-label fw-bold">Nombres <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    id="nombres"
                                    name="nombres" 
                                    class="form-control @error('nombres') is-invalid @enderror"
                                    value="{{ old('nombres', $usuario->nombres) }}"
                                    required
                                />
                                @error('nombres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="apellidos" class="form-label fw-bold">Apellidos <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    id="apellidos"
                                    name="apellidos" 
                                    class="form-control @error('apellidos') is-invalid @enderror"
                                    value="{{ old('apellidos', $usuario->apellidos) }}"
                                    required
                                />
                                @error('apellidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sexo" class="form-label fw-bold">Sexo</label>
                                <select 
                                    id="sexo"
                                    name="sexo" 
                                    class="form-select @error('sexo') is-invalid @enderror"
                                >
                                    <option value="">-- Selecciona --</option>
                                    <option value="masculino" @if(old('sexo', $usuario->sexo) === 'masculino') selected @endif>Masculino</option>
                                    <option value="femenino" @if(old('sexo', $usuario->sexo) === 'femenino') selected @endif>Femenino</option>
                                </select>
                                @error('sexo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label fw-bold">Teléfono</label>
                                <input 
                                    type="tel" 
                                    id="telefono"
                                    name="telefono" 
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    value="{{ old('telefono', $usuario->telefono) }}"
                                    placeholder="900000000"
                                />
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Datos de Contacto -->
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="bi bi-envelope me-2"></i>Datos de Contacto</h5>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
                            <input 
                                type="email" 
                                id="email"
                                name="email" 
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $usuario->email) }}"
                                required
                            />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Datos Adicionales (Cliente) -->
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="bi bi-file-earmark-person me-2"></i>Información Adicional del Cliente</h5>
                        <p class="text-muted small mb-3">Estos datos nos ayudan a proporcionarte un mejor servicio</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="documento_identidad" class="form-label fw-bold">Documento de Identidad</label>
                                <input 
                                    type="text" 
                                    id="documento_identidad"
                                    name="documento_identidad" 
                                    class="form-control @error('documento_identidad') is-invalid @enderror"
                                    value="{{ old('documento_identidad', $cliente->documento_identidad) }}"
                                    placeholder="DNI / Pasaporte"
                                />
                                @error('documento_identidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label fw-bold">Dirección</label>
                            <textarea 
                                id="direccion"
                                name="direccion" 
                                class="form-control @error('direccion') is-invalid @enderror"
                                rows="3"
                                placeholder="Ej: Av. Principal 123, Apto. 456"
                            >{{ old('direccion', $cliente->direccion) }}</textarea>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Información de Cuenta</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="text-muted mb-1"><small>ESTADO</small></p>
                    <span class="badge bg-{{ $usuario->estado === 'activo' ? 'success' : 'danger' }}">
                        {{ ucfirst($usuario->estado) }}
                    </span>
                </div>
                <hr>
                <div class="mb-3">
                    <p class="text-muted mb-1"><small>MIEMBRO DESDE</small></p>
                    <p class="mb-0 fw-bold">{{ $usuario->created_at->format('d/m/Y') }}</p>
                </div>
                <hr>
                <div class="mb-3">
                    <p class="text-muted mb-1"><small>ÚLTIMO ACCESO</small></p>
                    <p class="mb-0 fw-bold">
                        @if($usuario->ultimo_acceso_at)
                            {{ $usuario->ultimo_acceso_at->format('d/m/Y H:i') }}
                        @else
                            Primer acceso
                        @endif
                    </p>
                </div>
                <hr>
                <div class="mb-0">
                    <p class="text-muted mb-1"><small>PERFIL COMPLETADO</small></p>
                    @if($cliente && $cliente->documento_identidad && $cliente->direccion)
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>Completo
                        </span>
                    @else
                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-exclamation-triangle me-1"></i>Incompleto
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection




