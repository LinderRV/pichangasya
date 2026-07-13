<!DOCTYPE html>
<html lang="es">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>

  	   <!-- Title -->
	<title>Sistemas Pichanga ya</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="DexignZone">
	<meta name="robots" content="">

	<meta name="keywords" content="Desarrollar un sistema web orientado a la gestión integral de reservas de canchas deportivas que permita controlar  la disponibilidad, automatizar la asignación de horarios y centralizar la información operativa, con el fin de optimizar la eficiencia del negocio y minimizar pérdidas derivadas de una gestión manual ineficiente.">
	<meta name="description" content="Sistemas Pichanga ya es un desarrollo inmobiliario de primer nivel que ofrece una variedad de propiedades residenciales de alta calidad diseñadas para satisfacer las diversas necesidades y preferencias de los compradores. Con su compromiso con la excelencia y la atención al detalle, Sistemas Pichanga ya se ha establecido como una marca de confianza en la industria inmobiliaria.">

	<meta property="og:title" content="Sistemas Pichanga ya">
	<meta property="og:description" content="Sistemas Pichanga ya es un desarrollo inmobiliario de primer nivel que ofrece una variedad de propiedades residenciales de alta calidad diseñadas para satisfacer las diversas necesidades y preferencias de los compradores. Con su compromiso con la excelencia y la atención al detalle, Sistemas Pichanga ya se ha establecido como una marca de confianza en la industria inmobiliaria.">
	<meta property="og:image" content="../griya.dexignzone.com/xhtml/social-image.html">
	<meta name="format-detection" content="telephone=no">

	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Favicon icon -->
	<link rel="icon" type="image/png" href="/images/favicon.png">

	<!-- Bootstrap 5  -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

	<link href="/vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">

      <link href="/css/style.css" rel="stylesheet">
    <style>
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
        .social-btn {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.12);
        }
        .social-btn:hover { background: rgba(255,255,255,.16); }
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
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('web.paginas.inicio') || request()->is('/') ? 'active' : '' }}" href="{{ route('web.paginas.inicio') }}">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('web.paginas.canchas') || request()->routeIs('web.paginas.cancha') ? 'active' : '' }}" href="{{ route('web.paginas.canchas') }}">Canchas</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('web.paginas.inicio') }}#como-funciona">Cómo funciona</a></li>
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
                    <div class="col-12 col-lg-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="fw-bold fs-5">Pichangas<span style="color: var(--pya-green)">Ya</span></span>
                        </div>
                        <p class="muted mb-3">La mejor plataforma para reservar canchas deportivas de forma fácil y rápida.</p>
                    </div>

                    <div class="col-6 col-lg-2">
                        <div class="footer-title mb-3">Enlaces</div>
                        <ul class="list-unstyled d-grid gap-2 mb-0">
                            <li><a href="/">Inicio</a></li>
                            <li><a href="#canchas">Canchas</a></li>
                            <li><a href="#como-funciona">Como funciona</a></li>
                            <li><a href="#contactanos">Contáctanos</a></li>
                        </ul>
                    </div>

                    <div class="col-6 col-lg-2">
                        <div class="footer-title mb-3">Legal</div>
                        <ul class="list-unstyled d-grid gap-2 mb-0">
                            <li><a href="#">Términos y condiciones</a></li>
                            <li><a href="#">Política de privacidad</a></li>
                            <li><a href="#">Política de cookies</a></li>
                        </ul>
                    </div>

                    <div class="col-12 col-lg-3">
                        <div class="footer-title mb-3">Contacto</div>
                        <ul class="list-unstyled d-grid gap-2 mb-0">
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-telephone"></i><a href="tel:+51912345678">+51 912 345 678</a></li>
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-envelope"></i><a href="mailto:hola@pichangasya.com">hola@pichangasya.com</a></li>
                            <li class="d-flex align-items-center gap-2"><i class="bi bi-geo-alt"></i><span class="muted">Lima, Perú</span></li>
                        </ul>
                    </div>

                    <div class="col-12 col-lg-2">
                        <div class="footer-title mb-3">Síguenos</div>
                        <div class="d-flex gap-2">
                            <a class="social-btn" href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                            <a class="social-btn" href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                            <a class="social-btn" href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                        </div>
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
    <!-- Bootstrap 5 JS (navbar toggler) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

@yield('script')
</body>

</html>