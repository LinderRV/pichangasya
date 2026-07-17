<!DOCTYPE html>
<html lang="es">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>

	<title>@yield('title', 'PichangasYa | Reserva canchas deportivas')</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="PichangasYa">
	<meta name="robots" content="index,follow">
	<meta name="description" content="@yield('meta_description', 'Encuentra canchas deportivas, consulta horarios disponibles y confirma tu reserva online con PichangasYa.')">
	<link rel="canonical" href="{{ url()->current() }}">

	<meta property="og:type" content="website">
	<meta property="og:site_name" content="PichangasYa">
	<meta property="og:title" content="@yield('og_title', 'PichangasYa | Reserva canchas deportivas')">
	<meta property="og:description" content="@yield('meta_description', 'Encuentra canchas deportivas, consulta horarios disponibles y confirma tu reserva online con PichangasYa.')">
	<meta property="og:url" content="{{ url()->current() }}">
	<meta property="og:image" content="{{ asset('/images/logo-icon.png') }}">
	<meta name="format-detection" content="telephone=no">

	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Favicon icon -->
	<link rel="icon" type="image/png" href="/images/favicon.png">

	<!-- Bootstrap 5 and icons are served locally. -->
	<link href="/icons/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

	<link href="/vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">

      <link href="/css/style.css" rel="stylesheet">
    <style nonce="{{ request()->attributes->get('csp_nonce') }}">
        :root {
            --pya-green: #198754;
            --pya-dark: #0b1220;
        }

        body.public-web { background: #f6f8fb; }

        body.public-web .content-body,
        body.public-web #main-wrapper,
        body.public-web .deznav,
        body.public-web .nav-header { display: none !important; }

        .public-header {
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(10px);
        }

        .public-brand img.logo-abbr { width: 38px; height: 28px; object-fit: contain; }
        .public-brand img.brand-title { width: 128px; height: auto; }

        .public-nav .nav-link {
            font-weight: 700;
            color: #111827;
            border-radius: 12px;
            padding: .55rem .8rem;
        }
        .public-nav .nav-link:hover { background: rgba(25,135,84,.10); color: #145c35; }
        .public-nav .nav-link.active { color: var(--pya-green); }

        .btn-pill { border-radius: 999px; font-weight: 800; padding: .55rem 1rem; }
        .btn-outline-soft { border-color: rgba(2,6,23,.18); }

        main.public-main { padding: 18px 0 30px; }

        .public-footer {
            background: var(--pya-dark);
            color: rgba(255,255,255,.86);
        }
        .public-footer a { color: rgba(255,255,255,.82); text-decoration: none; }
        .public-footer a:hover { color: #fff; text-decoration: underline; }
        .public-footer .muted { color: rgba(255,255,255,.64); }
        .public-footer .footer-title { font-size: .95rem; font-weight: 800; letter-spacing: .2px; }
    </style>
      
@yield('link')
</head>
<body class="public-web">

    <header class="public-header sticky-top border-bottom">
        <nav class="navbar navbar-expand-lg py-2">
            <div class="container">
                <a href="/" class="public-brand navbar-brand d-inline-flex align-items-center gap-2" aria-label="Ir al inicio">
				<img class="logo-abbr" src="/images/logo-icon.png" alt="PichangasYa" width="48" height="36">
				<img class="brand-title" src="/images/logo-text.png" alt="PichangasYa">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="publicNavbar">
                    <ul class="public-nav navbar-nav ms-auto align-items-lg-center gap-lg-1">
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') || request()->routeIs('web.paginas.inicio') ? 'active' : '' }}" href="{{ route('home') }}">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('web.paginas.canchas') || request()->routeIs('web.paginas.cancha') ? 'active' : '' }}" href="{{ route('web.paginas.canchas') }}">Canchas</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#como-funciona">Cómo funciona</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('web.paginas.ayuda') ? 'active' : '' }}" href="{{ route('web.paginas.ayuda') }}">Ayuda</a></li>
                    </ul>

                    <div class="d-flex align-items-lg-center gap-2 ms-lg-3 mt-3 mt-lg-0">
                        @guest
                            <a class="btn btn-outline-secondary btn-outline-soft btn-pill" href="/login">Iniciar sesión</a>
                            <a class="btn btn-success btn-pill" href="/register">Registrarse</a>
                        @else
                            <a class="btn btn-outline-secondary btn-outline-soft btn-pill" href="{{ Auth::user()->esCliente() ? route('cliente.reservas') : route('dashboard') }}">
                                <i class="bi bi-person-circle me-1"></i>Mi cuenta
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-pill">Cerrar sesión</button>
                            </form>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>
    </header>
		
		
    <main class="public-main">
    <div class="container">
        @yield('content')
        </div>
    </main>
		
		
		
        <!--**********************************
            Footer start
        ***********************************-->
        <footer class="public-footer pt-5">
            <div class="container">
                <div class="row g-4 align-items-start pb-4">
                    <div class="col-12 col-lg-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="fw-bold fs-5">Pichangas<span style="color: var(--pya-green)">Ya</span></span>
                        </div>
                        <p class="muted mb-3">La mejor plataforma para reservar canchas deportivas de forma fácil y rápida.</p>
                    </div>

                    <div class="col-6 col-lg-2">
                        <div class="footer-title mb-3">Enlaces</div>
                        <ul class="list-unstyled d-grid gap-2 mb-0">
                            <li><a href="{{ route('home') }}">Inicio</a></li>
                            <li><a href="{{ route('web.paginas.canchas') }}">Canchas</a></li>
                            <li><a href="{{ route('home') }}#como-funciona">Cómo funciona</a></li>
                        </ul>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="footer-title mb-3">Legal</div>
                        <ul class="list-unstyled d-grid gap-2 mb-0">
                            <li><a href="{{ route('web.paginas.terminos') }}">Términos y condiciones</a></li>
                            <li><a href="{{ route('web.paginas.privacidad') }}">Política de privacidad y cookies</a></li>
                        </ul>
                    </div>

                    <div class="col-12 col-lg-3">
                        <div class="footer-title mb-3">Ayuda y seguridad</div>
                        <ul class="list-unstyled d-grid gap-2 mb-0">
                            <li><a href="{{ route('web.paginas.ayuda') }}">Centro de ayuda</a></li>
                            <li><span class="muted"><i class="bi bi-shield-check me-1"></i>Pago procesado mediante Stripe</span></li>
                            <li><span class="muted">Cada complejo publica sus propios datos de contacto.</span></li>
                        </ul>
                    </div>
                </div>

                <div class="border-top" style="border-color: rgba(255,255,255,.10) !important;"></div>
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 py-3">
                    <div class="muted">© <?php echo date('Y'); ?> PichangasYa. Todos los derechos reservados.</div>
                    <div class="muted">Desarrollado por Linder RV</div>
                </div>
            </div>
        </footer>
        <!--**********************************
            Footer end
        ***********************************-->



    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Local bundle: Bootstrap 5, Popper and jQuery. -->
    <script src="/vendor/global/global.min.js"></script>

@yield('script')
</body>

</html>
