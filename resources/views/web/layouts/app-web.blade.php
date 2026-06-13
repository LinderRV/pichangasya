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

        .public-brand svg.logo-abbr { width: 38px; height: 38px; }
        .public-brand svg.brand-title { width: 128px; height: auto; }

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
				<svg class="logo-abbr" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect class="rect-primary-rect" width="64" height="64" rx="18" fill="#216FED"/>
					<path d="M33.9126 48.6459H16.7709C15.9917 48.6459 15.3542 48.0084 15.3542 47.2292V22.9334C15.3542 22.1542 15.9917 21.5167 16.7709 21.5167H17.6209C27.3959 21.5167 35.3292 29.45 35.3292 39.225V47.2292C35.2584 48.0084 34.6917 48.6459 33.9126 48.6459ZM18.1167 45.8834H32.4959V39.225C32.4959 31.15 26.1209 24.6334 18.1167 24.35V45.8834Z" fill="#F2F6FC"/>
					<path d="M47.2291 48.6459H30.0874C29.3083 48.6459 28.6708 48.0084 28.6708 47.2292C28.6708 46.45 29.3083 45.8125 30.0874 45.8125H45.8833V33.0625C45.8833 24.9875 39.5083 18.4709 31.5041 18.1875V28.2459C31.5041 29.025 30.8666 29.6625 30.0874 29.6625C29.3083 29.6625 28.6708 29.025 28.6708 28.2459V16.7709C28.6708 15.9917 29.3083 15.3542 30.0874 15.3542H30.9374C40.7124 15.3542 48.6458 23.2875 48.6458 33.0625V47.3C48.6458 48.0084 48.0083 48.6459 47.2291 48.6459Z" fill="#F2F6FC"/>
					<path d="M28.246 48.6458H22.296C21.5169 48.6458 20.8794 48.0083 20.8794 47.2292V37.95C20.8794 37.1709 21.5169 36.5334 22.296 36.5334H28.246C29.0252 36.5334 29.6627 37.1709 29.6627 37.95V47.2292C29.6627 48.0083 29.0252 48.6458 28.246 48.6458ZM23.7127 45.8833H26.8294V39.3667H23.7127V45.8833Z" fill="#F2F6FC"/>
				</svg>
				<svg class="brand-title" width="108" height="44" viewBox="0 0 108 44" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path class="svg-title-path" d="M11.0979 43.58C7.81657 43.58 5.2559 43.0893 3.4159 42.108C1.60657 41.1573 0.701904 39.5933 0.701904 37.416C0.701904 36.2813 0.962571 35.2693 1.4839 34.38C2.0359 33.5213 2.87924 32.632 4.0139 31.712C3.4619 31.344 3.03257 30.8687 2.7259 30.286C2.4499 29.7033 2.3119 29.09 2.3119 28.446C2.3119 27.986 2.41924 27.5107 2.6339 27.02C2.87924 26.5293 3.3699 25.7013 4.1059 24.536C2.02057 23.0027 0.977904 20.764 0.977904 17.82C0.977904 15.0293 1.82124 12.9593 3.5079 11.61C5.22524 10.23 7.6019 9.53999 10.6379 9.53999C11.9259 9.53999 13.4746 9.73933 15.2839 10.138L22.6899 9.90799V14.83L19.2399 14.554C19.6386 15.198 19.8992 15.75 20.0219 16.21C20.1752 16.67 20.2519 17.2987 20.2519 18.096C20.2519 20.856 19.4699 22.834 17.9059 24.03C16.3419 25.1953 13.9652 25.778 10.7759 25.778C9.8559 25.778 9.15057 25.7167 8.6599 25.594C8.35324 26.33 8.1999 26.9587 8.1999 27.48C8.1999 28.124 8.50657 28.5227 9.1199 28.676C9.7639 28.7987 10.9446 28.8753 12.6619 28.906C15.0232 28.9673 16.8939 29.1513 18.2739 29.458C19.6846 29.7647 20.7732 30.3933 21.5399 31.344C22.3066 32.264 22.6899 33.6287 22.6899 35.438C22.6899 38.2287 21.7392 40.2833 19.8379 41.602C17.9366 42.9207 15.0232 43.58 11.0979 43.58ZM10.6839 21.086C13.0146 21.086 14.1799 19.9513 14.1799 17.682C14.1799 15.4433 13.0146 14.324 10.6839 14.324C8.32257 14.324 7.1419 15.4433 7.1419 17.682C7.1419 19.9513 8.32257 21.086 10.6839 21.086ZM11.5119 38.52C13.2599 38.52 14.5172 38.3207 15.2839 37.922C16.0812 37.5233 16.4799 36.91 16.4799 36.082C16.4799 35.3153 16.1886 34.8247 15.6059 34.61C15.0539 34.426 14.3946 34.3187 13.6279 34.288C12.8919 34.2573 12.4012 34.242 12.1559 34.242L8.0159 33.92C7.18791 34.748 6.7739 35.576 6.7739 36.404C6.7739 37.14 7.15724 37.6767 7.9239 38.014C8.72124 38.3513 9.91724 38.52 11.5119 38.52Z" fill="#273240"/>
					<path class="svg-title-path" d="M26.4242 10H32.5422V12.438C35.1795 10.9047 37.6328 9.90799 39.9022 9.44799V15.658C37.6022 16.118 35.4708 16.6547 33.5082 17.268L32.5882 17.59V33H26.4242V10Z" fill="#273240"/>
					<path 	class="svg-title-path" d="M43.5394 10H49.7034V33H43.5394V10ZM43.5394 0.845993H49.7034V7.056H43.5394V0.845993Z" fill="#273240"/>
					<path  class="svg-title-path" d="M62.591 33H58.267L52.609 10H58.681L62.867 27.756H63.925L68.111 10H74.183L66.087 42.66H60.061L62.591 33Z" fill="#273240"/>
					<path class="svg-title-path" d="M82.9776 33.552C78.2242 33.552 75.8476 31.0373 75.8476 26.008C75.8476 23.616 76.4762 21.8987 77.7336 20.856C79.0216 19.7827 81.0762 19.154 83.8976 18.97L88.8196 18.602V17.176C88.8196 16.256 88.6049 15.612 88.1756 15.244C87.7769 14.876 87.0869 14.692 86.1056 14.692C85.0936 14.692 83.6982 14.738 81.9196 14.83C80.1409 14.8913 78.6536 14.968 77.4576 15.06L77.2736 10.782C80.5856 9.89266 83.7136 9.44799 86.6576 9.44799C89.6016 9.44799 91.7176 10.0613 93.0056 11.288C94.3242 12.5147 94.9836 14.4773 94.9836 17.176V26.744C95.0449 27.5107 95.1676 28.032 95.3516 28.308C95.5662 28.584 95.9496 28.7833 96.5016 28.906L96.3176 33.552C94.7229 33.552 93.4502 33.4447 92.4996 33.23C91.5796 33.0153 90.6596 32.586 89.7396 31.942C87.5009 33.0153 85.2469 33.552 82.9776 33.552ZM84.3116 28.538C85.4769 28.538 86.7496 28.354 88.1296 27.986L88.8196 27.802V22.88L84.6336 23.248C82.9162 23.4013 82.0576 24.3213 82.0576 26.008C82.0576 27.6947 82.8089 28.538 84.3116 28.538Z" fill="#273240"/>
					<path class="svg-title-path" d="M100.584 25.364H107.208V33H100.584V25.364Z" fill="#273240"/>
				</svg>
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
                        <a class="btn btn-outline-secondary btn-outline-soft btn-pill" href="/login">Iniciar sesión</a>
                        <a class="btn btn-success btn-pill" href="/register">Registrarse</a>
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