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

	<meta name="keywords" content="Desarrollar un sistema web orientado a la gestiÃ³n integral de reservas de canchas deportivas que permita controlar en tiempo real la disponibilidad, automatizar la asignaciÃ³n de horarios y centralizar la informaciÃ³n operativa, con el fin de optimizar la eficiencia del negocio y minimizar pÃ©rdidas derivadas de una gestiÃ³n manual ineficiente.">
	<meta name="description" content="Sistemas Pichanga ya es un desarrollo inmobiliario de primer nivel que ofrece una variedad de propiedades residenciales de alta calidad diseÃ±adas para satisfacer las diversas necesidades y preferencias de los compradores. Con su compromiso con la excelencia y la atenciÃ³n al detalle, Sistemas Pichanga ya se ha establecido como una marca de confianza en la industria inmobiliaria.">

	<meta property="og:title" content="Sistemas Pichanga ya">
	<meta property="og:description" content="Sistemas Pichanga ya es un desarrollo inmobiliario de primer nivel que ofrece una variedad de propiedades residenciales de alta calidad diseÃ±adas para satisfacer las diversas necesidades y preferencias de los compradores. Con su compromiso con la excelencia y la atenciÃ³n al detalle, Sistemas Pichanga ya se ha establecido como una marca de confianza en la industria inmobiliaria.">
	<meta property="og:image" content="../griya.dexignzone.com/xhtml/social-image.html">
	<meta name="format-detection" content="telephone=no">

	<!-- Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Favicon icon -->
	<link rel="icon" type="image/png" href="/images/favicon.png">

	<link href="/vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">
    <!-- Datatable -->
    <link href="/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
	<!-- Style css -->
      <link href="/css/style.css" rel="stylesheet">
      <link href="/vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
      <link href="/vendor/toastr/css/toastr.min.css" rel="stylesheet">

      @yield('link')

</head>
<body>

    <!-- Overlay de carga para AJAX (GS.inicioSolicitud / GS.finSolicitud) -->
    <div id="divLoading" style="display:none; position:fixed; inset:0; z-index:1080; background:rgba(11,18,32,.45); align-items:center; justify-content:center;">
        <div class="spinner-border text-light" role="status" style="width:3rem; height:3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
		<div class="lds-ripple">
			<div></div>
			<div></div>
		</div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="/" class="brand-logo">
				<img class="logo-abbr" src="/images/logo-icon.png" alt="PichangasYa" width="48" height="36">
				<img class="brand-title" src="/images/logo-text.png" alt="PichangasYa">
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

		<!--**********************************
            Chat box start
        ***********************************-->

		<!--**********************************
            Chat box End
        ***********************************-->

		<!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <ul class="navbar-nav header-right ms-auto">
							<li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link bell dz-theme-mode" href="javascript:void(0);">
									<i id="icon-light" class="fas fa-sun"></i>
                                    <i id="icon-dark" class="fas fa-moon"></i>

                                </a>
							</li>

							<li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
									<div class="header-info me-3">
										<span class="fs-18 font-w500 text-end">{{ Auth::user()->nombres }}</span>
										<small class="text-end fs-14 font-w400">{{ Auth::user()->email }}</small>
									</div>
                                    <img src="/images/pic1.jpg" width="20" alt="">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if(Auth::user()->esCliente())
                                        <a href="{{ route('cliente.perfil') }}" class="dropdown-item ai-icon">
                                            <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                            <span class="ms-2">Perfil </span>
                                        </a>
                                    @endif

                                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item ai-icon" style="border:none; background:none; width:100%; text-align:left; padding: .5rem 1rem; cursor:pointer;">
                                            <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                            <span class="ms-2">Cerrar sesión </span>
                                        </button>
                                    </form>
                                </div>
                            </li>
							<li class="nav-item">

							</li>
                        </ul>
                    </div>
				</nav>
			</div>
		</div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
       <div class="deznav">
            <div class="deznav-scroll">
				<ul class="metismenu" id="menu">

                    @if(Auth::user()->esCliente())
                        <li><a href="{{ route('cliente.perfil') }}" aria-expanded="false">
                                <i class="flaticon-050-info"></i>
                                <span class="nav-text">Mi Perfil</span>
                            </a>
                        </li>
                        <li><a href="{{ route('cliente.reservas') }}" aria-expanded="false">
                                <i class="flaticon-086-star"></i>
                                <span class="nav-text">Mis Reservas</span>
                            </a>
                        </li>
                    @else
                        <li><a href="{{ route('dashboard') }}" aria-expanded="false">
                            <i class="flaticon-025-dashboard"></i>
                            <span class="nav-text">Inicio</span>
                        </a></li>

                        @if(Auth::user()->esSuperAdmin())
                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                                <i class="flaticon-050-info"></i>
                                <span class="nav-text">Gestión Usuarios</span>
                            </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
                                    <li><a href="{{ route('admin.rol.index') }}">Roles</a></li>
                                </ul>
                            </li>

                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                                <i class="flaticon-041-graph"></i>
                                <span class="nav-text">Complejos Deportivos</span>
                            </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('admin.complejos.index') }}">Complejos</a></li>
                                    <li><a href="{{ route('admin.complejos.asignacion.index') }}">Asignar Dueño</a></li>
                                    <li><a href="{{ route('admin.canchas.index') }}">Canchas</a></li>
                                    <li><a href="{{ route('admin.horarios.index') }}">Horarios</a></li>
                                    <li><a href="{{ route('admin.bloqueos.index') }}">Disponibilidad</a></li>
                                </ul>
                            </li>

                            <li><a href="{{ route('admin.reservas.index') }}" aria-expanded="false">
                                <i class="flaticon-086-star"></i>
                                <span class="nav-text">Reservas</span>
                            </a></li>

                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                                <i class="flaticon-045-heart"></i>
                                <span class="nav-text">Pagos</span>
                            </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('admin.pagos.index') }}">Historial de pagos</a></li>
                                    <li><a href="{{ route('admin.metodospago.index') }}">Métodos de pago</a></li>
                                </ul>
                            </li>

                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                                <i class="flaticon-072-printer"></i>
                                <span class="nav-text">Reportes</span>
                            </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('admin.reportes.reservas.index') }}">Reporte de reservas</a></li>
                                    <li><a href="{{ route('admin.reportes.ingresos.index') }}">Reporte de ingresos</a></li>
                                </ul>
                            </li>
                        @else
                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                                <i class="flaticon-086-star"></i>
                                <span class="nav-text">Mi Complejo</span>
                            </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('admin.canchas.index') }}">Canchas</a></li>
                                    <li><a href="{{ route('admin.horarios.index') }}">Horarios</a></li>
                                    <li><a href="{{ route('admin.bloqueos.index') }}">Disponibilidad</a></li>
                                </ul>
                            </li>

                            <li><a href="{{ route('admin.reservas.index') }}" aria-expanded="false">
                                <i class="flaticon-045-heart"></i>
                                <span class="nav-text">Reservas</span>
                            </a></li>

                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                                <i class="flaticon-072-printer"></i>
                                <span class="nav-text">Reportes</span>
                            </a>
                                <ul aria-expanded="false">
                                    <li><a href="{{ route('admin.reportes.ingresos.index') }}">Reporte de ingresos</a></li>
                                </ul>
                            </li>
                        @endif
                    @endif
                </ul>

			</div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->

		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid pt-3">
				 @yield('content')

            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->



        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
             <p>Copyright © Pichanga ya  &amp; Developed by <a href="#" target="_blank">Linder RV</a> <?php echo date("Y"); ?> </p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->

		<!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->


	</div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
   <script src="/vendor/global/global.min.js"></script>
	<script src="/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>


    <!-- Datatable -->
    <script src="/vendor/datatables/js/jquery.dataTables.min.js"></script>


    <script src="/js/custom.min.js"></script>
	<script src="/js/deznav-init.js"></script>
	<script src="/js/demo.js"></script>

    <!-- Helpers GS (SweetAlert2 + Toastr locales, Toastify por CDN) -->
    <script src="/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/vendor/toastr/js/toastr.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="/pichanga/js/funciones.js"></script>

    @yield('script')
</body>

</html>
