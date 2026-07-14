@extends('web.layouts.app-web')

@section('title', 'Términos y condiciones | PichangasYa')
@section('meta_description', 'Condiciones de uso, reservas, pagos, cancelaciones y reembolsos aplicables en PichangasYa.')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="link-success">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Términos y condiciones</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <article class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-lg-5">
                <span class="badge bg-success-subtle text-success mb-3">Vigentes desde el 14 de julio de 2026</span>
                <h1 class="fw-bold mb-3">Términos y condiciones de uso</h1>
                <p class="text-muted">Estas condiciones explican cómo se utiliza PichangasYa para consultar disponibilidad, reservar canchas y gestionar pagos.</p>

                <h2 class="h5 fw-bold mt-4">1. Alcance del servicio</h2>
                <p>PichangasYa conecta a clientes con complejos deportivos registrados. Cada complejo administra sus canchas, precios, horarios, bloqueos, datos de contacto y atención operativa.</p>

                <h2 class="h5 fw-bold mt-4">2. Cuenta del usuario</h2>
                <p>Para reservar se requiere una cuenta activa y datos correctos. El usuario es responsable de mantener la confidencialidad de sus credenciales y de actualizar su información de contacto.</p>

                <h2 class="h5 fw-bold mt-4">3. Disponibilidad y confirmación</h2>
                <p>Los horarios se consultan en tiempo real. Una selección no queda reservada hasta que el pago sea aprobado y el sistema genere un código de reserva. Si el horario deja de estar disponible antes de la confirmación, la operación no debe considerarse completada.</p>

                <h2 class="h5 fw-bold mt-4">4. Precios y pagos</h2>
                <p>Antes de pagar se muestran la cancha, fecha, horario, duración y total. Los pagos online se procesan mediante Niubiz. PichangasYa no debe almacenar el número completo de tarjeta ni el código de seguridad.</p>

                <h2 class="h5 fw-bold mt-4">5. Cancelaciones y reprogramaciones</h2>
                <p>Las solicitudes se coordinan con el complejo mediante los datos publicados en la ficha de la cancha y en “Mis reservas”. Su aprobación depende de la disponibilidad, anticipación y condiciones comunicadas por el establecimiento.</p>

                <h2 class="h5 fw-bold mt-4">6. Reembolsos</h2>
                <p>Cuando corresponda un reembolso, el complejo registra el monto, método, fecha y referencia de la operación. El cliente puede consultar el estado actualizado de su reserva y los datos registrados del reembolso.</p>

                <h2 class="h5 fw-bold mt-4">7. Uso permitido</h2>
                <p>No está permitido intentar reservar fraudulentamente, afectar la disponibilidad, suplantar identidades, vulnerar cuentas o utilizar información de otros usuarios sin autorización.</p>

                <h2 class="h5 fw-bold mt-4">8. Atención de incidencias</h2>
                <p>Las consultas sobre acceso a la cancha, horarios, cancelaciones y reembolsos deben dirigirse al complejo. Para orientación general consulta el <a href="{{ route('web.paginas.ayuda') }}" class="link-success fw-semibold">Centro de ayuda</a>.</p>
                @if($supportEmail)
                    <p>Soporte de la plataforma: <a href="mailto:{{ $supportEmail }}" class="link-success">{{ $supportEmail }}</a>.</p>
                @endif

                <h2 class="h5 fw-bold mt-4">9. Cambios en las condiciones</h2>
                <p class="mb-0">Las condiciones pueden actualizarse cuando cambien las funciones o procesos del servicio. La versión vigente y su fecha siempre estarán publicadas en esta página.</p>
            </div>
        </article>
    </div>
</div>
@endsection
