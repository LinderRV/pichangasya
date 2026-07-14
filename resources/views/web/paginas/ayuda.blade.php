@extends('web.layouts.app-web')

@section('title', 'Centro de ayuda | PichangasYa')
@section('meta_description', 'Guía para reservar, pagar, reprogramar y consultar reembolsos en PichangasYa.')

@section('content')
<div class="text-center py-4 py-lg-5">
    <span class="badge bg-success-subtle text-success mb-3">Centro de ayuda</span>
    <h1 class="fw-bold">¿Cómo podemos orientarte?</h1>
    <p class="text-muted mb-0">Respuestas claras para completar y gestionar tu reserva.</p>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['bi-search', '1. Busca', 'Filtra canchas por distrito y tipo de deporte.'],
        ['bi-calendar-check', '2. Selecciona', 'Elige fecha, duración y uno de los horarios disponibles.'],
        ['bi-credit-card', '3. Paga', 'Revisa el total y completa el pago seguro mediante Niubiz.'],
        ['bi-check-circle', '4. Confirma', 'Recibe tu código y consulta la reserva desde tu cuenta.'],
    ] as [$icon, $title, $text])
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <i class="bi {{ $icon }} fs-2 text-success"></i>
                    <h2 class="h6 fw-bold mt-3">{{ $title }}</h2>
                    <p class="text-muted small mb-0">{{ $text }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="accordion shadow-sm rounded-4 overflow-hidden mb-4" id="faqAyuda">
    @php
        $preguntas = [
            ['¿Cuándo queda confirmada mi reserva?', 'Cuando el pago es aprobado y el sistema genera un código de reserva. Seleccionar un horario no lo bloquea de forma definitiva antes de esa confirmación.'],
            ['¿Dónde encuentro el contacto del complejo?', 'En la ficha de la cancha y en la tabla “Mis reservas”. Si el complejo registró teléfono, aparecerá un acceso directo a WhatsApp.'],
            ['¿Cómo solicito una reprogramación?', 'Contacta al complejo con tu código de reserva. El administrador verificará otra fecha y horario disponible antes de registrar el cambio.'],
            ['¿Cómo solicito una cancelación o reembolso?', 'Contacta al complejo e indica el código de reserva. Si la cancelación y el reembolso proceden, el establecimiento registrará el monto, método y fecha.'],
            ['¿Dónde descargo mi comprobante?', 'Ingresa a “Mi cuenta”, abre “Mis reservas” y utiliza el botón de comprobante disponible para las reservas pagadas.'],
            ['¿Qué hago si el pago no se confirma?', 'No repitas el pago inmediatamente. Revisa “Mis reservas” y, si no existe una reserva confirmada, comunícate con el complejo o con el soporte oficial de la plataforma.'],
        ];
    @endphp
    @foreach($preguntas as $index => [$question, $answer])
        <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header">
                <button class="accordion-button {{ $index ? 'collapsed' : '' }} fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}" aria-expanded="{{ $index ? 'false' : 'true' }}">
                    {{ $question }}
                </button>
            </h2>
            <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index ? '' : 'show' }}" data-bs-parent="#faqAyuda">
                <div class="accordion-body text-muted">{{ $answer }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="card border-success-subtle bg-success-subtle rounded-4">
    <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h2 class="h5 fw-bold mb-1">¿Necesitas atención sobre una reserva?</h2>
            <p class="mb-0 text-muted">Utiliza el contacto real del complejo mostrado en la ficha de la cancha o en “Mis reservas”.</p>
        </div>
        @if($supportEmail)
            <a href="mailto:{{ $supportEmail }}" class="btn btn-success btn-pill"><i class="bi bi-envelope me-2"></i>Soporte de la plataforma</a>
        @else
            <a href="{{ route('web.paginas.canchas') }}" class="btn btn-success btn-pill">Ver canchas</a>
        @endif
    </div>
</div>
@endsection
