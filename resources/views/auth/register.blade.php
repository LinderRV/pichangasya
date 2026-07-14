<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrarse | PichangasYa</title>
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/icons/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/vendor/toastr/css/toastr.min.css" rel="stylesheet">
    <style nonce="{{ request()->attributes->get('csp_nonce') }}">
        :root{ --pya-green:#198754; --pya-dark:#0b1220; }
        body{ background:#f6f8fb; }
        .auth-shell{ min-height:100vh; display:flex; }
        .auth-aside{ display:none; background:var(--pya-dark); position:relative; overflow:hidden; }
        .auth-aside:before{
            content:"";
            position:absolute;
            inset:0;
            background-image:
                linear-gradient(90deg, rgba(11,18,32,.82) 0%, rgba(11,18,32,.50) 55%, rgba(11,18,32,.18) 100%),
                url('/images/1.jpg');
            background-size:cover;
            background-position:center;
            transform:scale(1.04);
            filter:saturate(1.05);
        }
        .auth-aside > .content{ position:relative; z-index:1; padding:42px; color:#fff; }
        .brand{ display:inline-flex; align-items:center; gap:.6rem; font-weight:900; letter-spacing:-.01em; }
        .brand .dot{ color:var(--pya-green); }
        .auth-card{ border:1px solid rgba(2,6,23,.08); border-radius:18px; box-shadow:0 18px 50px rgba(2,6,23,.12); }
        .form-control{ border-radius:14px; padding:.75rem .95rem; border-color:rgba(2,6,23,.12); font-weight:700; }
        .form-control:focus{ border-color:rgba(25,135,84,.55); box-shadow:0 0 0 .25rem rgba(25,135,84,.14); }
        .input-group-text{ border-radius:14px; border-color:rgba(2,6,23,.12); background:#fff; }
        .btn-pill{ border-radius:999px; font-weight:900; padding:.7rem 1.05rem; }
        .muted{ color:rgba(17,24,39,.62); }
        .small-link{ font-weight:800; text-decoration:none; }
        .small-link:hover{ text-decoration:underline; }
        @media (min-width: 992px){
            .auth-aside{ display:block; width:46%; }
            .auth-main{ width:54%; }
        }
    </style>
</head>
<body>
    <div id="divLoading" style="display:none; position:fixed; inset:0; z-index:1080; background:rgba(11,18,32,.45); align-items:center; justify-content:center;">
        <div class="spinner-border text-light" role="status" style="width:3rem; height:3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <div class="auth-shell">
        <aside class="auth-aside" aria-hidden="true">
            <div class="content">
                <a class="brand text-white text-decoration-none" href="/" aria-label="Volver al inicio">
                    <i class="bi bi-dribbble" aria-hidden="true"></i>
                    <span>Pichangas<span class="dot">Ya</span></span>
                </a>
                <h1 class="display-6 fw-black fw-bold mt-4" style="max-width: 420px; line-height:1.05;">
                    Crea tu cuenta y empieza a reservar.
                </h1>
                <p class="mb-0" style="max-width: 460px; color: rgba(255,255,255,.86); font-weight:600;">
                    Regístrate para guardar tus datos y administrar tus reservas.
                </p>
            </div>
        </aside>

        <main class="auth-main flex-grow-1 d-flex align-items-center">
            <div class="container py-4" style="max-width: 520px;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <a class="brand text-decoration-none" href="/" aria-label="Volver al inicio">
                        <i class="bi bi-dribbble" aria-hidden="true" style="color: var(--pya-green)"></i>
                        <span class="text-dark">Pichangas<span style="color: var(--pya-green)">Ya</span></span>
                    </a>
                </div>

                <div class="card auth-card">
                    <div class="card-body p-4 p-lg-4">
                        <h2 class="h4 fw-bold mb-1">Crear cuenta</h2>
                        <p class="muted mb-4">Completa tus datos para registrarte.</p>

                        <form id="formRegistrar" method="POST" action="{{ route('register') }}" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="nombres" class="form-label fw-bold">Nombres</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person" aria-hidden="true"></i></span>
                                    <input
                                        id="nombres"
                                        name="nombres"
                                        type="text"
                                        class="form-control"
                                        value="{{ old('nombres') }}"
                                        required
                                        autofocus
                                        autocomplete="name"
                                        placeholder="Tus nombres"
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="apellidos" class="form-label fw-bold">Apellidos</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-vcard" aria-hidden="true"></i></span>
                                    <input
                                        id="apellidos"
                                        name="apellidos"
                                        type="text"
                                        class="form-control"
                                        value="{{ old('apellidos') }}"
                                        required
                                        autocomplete="family-name"
                                        placeholder="Tus apellidos"
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Correo</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        class="form-control"
                                        value="{{ old('email') }}"
                                        required
                                        autocomplete="username"
                                        placeholder="tu@correo.com"
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="clave" class="form-label fw-bold">Clave</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock" aria-hidden="true"></i></span>
                                    <input
                                        id="clave"
                                        name="clave"
                                        type="password"
                                        class="form-control"
                                        required
                                        autocomplete="new-password"
                                        placeholder="Mínimo 8 caracteres"
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="clave_confirmation" class="form-label fw-bold">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-check" aria-hidden="true"></i></span>
                                    <input
                                        id="clave_confirmation"
                                        name="clave_confirmation"
                                        type="password"
                                        class="form-control"
                                        required
                                        autocomplete="new-password"
                                        placeholder="Confirma tu contraseña"
                                    />
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-pill" style="height:50px">
                                    <i class="bi bi-person-plus me-2" aria-hidden="true"></i>Registrarme
                                </button>
                            </div>

                            <div class="text-center mt-3">
                                <span class="muted">¿Ya tienes cuenta?</span>
                                <a class="small-link text-success ms-1" href="{{ route('login') }}">Inicia sesión</a>
                            </div>
                        </form>
                    </div>
                </div>

                <p class="text-center muted small mt-3 mb-0">© {{ date('Y') }} PichangasYa</p>
            </div>
        </main>
    </div>

    <script src="/vendor/global/global.min.js"></script>
    <script src="/vendor/toastr/js/toastr.min.js"></script>
    <script src="/pichanga/js/funciones.js"></script>
    <script nonce="{{ request()->attributes->get('csp_nonce') }}">
        $(function () {
            $("#formRegistrar").on("submit", function (e) {
                e.preventDefault();
                const $form = $(this);

                $.ajax({
                    url: $form.attr("action"),
                    type: "POST",
                    data: $form.serialize(),
                    dataType: "json",
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                    beforeSend: function () {
                        $form.find(".is-invalid").removeClass("is-invalid");
                        $form.find(".invalid-feedback").remove();
                        GS.inicioSolicitud();
                    },
                })
                .done(function (resp) {
                    GS.toastSuccess(resp.message || "Cuenta creada.");
                    const destino = resp.data && resp.data.redirect ? resp.data.redirect : "/";
                    setTimeout(function () { window.location.href = destino; }, 700);
                })
                .fail(function (xhr) {
                    GS.finSolicitud();
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errores = xhr.responseJSON.errors;
                        Object.keys(errores).forEach(function (campo) {
                            const $input = $form.find("[name='" + campo + "']");
                            $input.addClass("is-invalid");
                            $input.closest(".input-group").after('<div class="invalid-feedback d-block">' + errores[campo][0] + "</div>");
                        });
                        GS.toastError(xhr.responseJSON.message || "Revisa los datos ingresados.");
                    } else {
                        GS.toastError("Ocurrió un error. Inténtalo nuevamente.");
                    }
                });
            });
        });
    </script>
</body>
</html>
