@extends('web.layouts.app-web')

@section('title', 'Política de privacidad y cookies | PichangasYa')
@section('meta_description', 'Información sobre datos personales y cookies técnicas utilizadas por PichangasYa.')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="link-success">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Privacidad y cookies</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-12 col-xl-9">
        <article class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-lg-5">
                <span class="badge bg-success-subtle text-success mb-3">Actualizada el 14 de julio de 2026</span>
                <h1 class="fw-bold mb-3">Política de privacidad y cookies</h1>
                <p class="text-muted">Esta política resume qué información utiliza PichangasYa y con qué finalidad.</p>

                <h2 class="h5 fw-bold mt-4">1. Datos utilizados</h2>
                <p>El sistema puede tratar nombres, apellidos, correo, teléfono, sexo, documento de identidad, dirección, credenciales protegidas, datos de reservas, pagos, cancelaciones y reembolsos.</p>

                <h2 class="h5 fw-bold mt-4">2. Finalidades</h2>
                <ul>
                    <li>Crear y proteger la cuenta del usuario.</li>
                    <li>Consultar disponibilidad y confirmar reservas.</li>
                    <li>Procesar pagos y emitir comprobantes.</li>
                    <li>Comunicar confirmaciones, reprogramaciones y cancelaciones.</li>
                    <li>Atender incidencias y prevenir operaciones fraudulentas.</li>
                    <li>Generar reportes operativos y financieros para cada complejo.</li>
                </ul>

                <h2 class="h5 fw-bold mt-4">3. Pagos</h2>
                <p>Los pagos online se procesan mediante Stripe. Los datos sensibles de la tarjeta deben enviarse al proveedor de pagos y no conservarse en la base de datos de PichangasYa.</p>

                <h2 class="h5 fw-bold mt-4">4. Acceso a la información</h2>
                <p>Los complejos acceden únicamente a la información necesaria para administrar sus canchas, reservas, pagos y atención al cliente. El acceso administrativo se encuentra restringido por roles.</p>

                <h2 class="h5 fw-bold mt-4">5. Conservación y seguridad</h2>
                <p>La información se conserva durante el tiempo necesario para prestar el servicio, mantener trazabilidad contable y atender controversias. Se aplican controles de autenticación, autorización, cifrado de contraseñas, protección CSRF y políticas de seguridad del navegador.</p>

                <h2 class="h5 fw-bold mt-4">6. Cookies</h2>
                <p>PichangasYa utiliza cookies técnicas indispensables para mantener la sesión, proteger formularios y conservar temporalmente el proceso de pago. Actualmente no se declaran cookies publicitarias ni de seguimiento de terceros.</p>

                <h2 class="h5 fw-bold mt-4">7. Derechos y actualización de datos</h2>
                <p>El usuario puede actualizar sus datos desde “Mi perfil”. Para solicitar revisión, corrección adicional o eliminación cuando corresponda, debe utilizar el canal oficial de soporte publicado por la plataforma.</p>
                @if($supportEmail)
                    <p>Canal de privacidad: <a href="mailto:{{ $supportEmail }}" class="link-success">{{ $supportEmail }}</a>.</p>
                @endif

                <h2 class="h5 fw-bold mt-4">8. Cambios en esta política</h2>
                <p class="mb-0">Cualquier cambio relevante será publicado aquí con una nueva fecha de actualización. La versión final para producción debe ser revisada junto con la identidad y canales oficiales del responsable del servicio.</p>
            </div>
        </article>
    </div>
</div>
@endsection
