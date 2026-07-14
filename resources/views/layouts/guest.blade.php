<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PichangasYa') }}</title>
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/icons/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/vendor/toastr/css/toastr.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="min-vh-100 d-flex align-items-center py-5">
        <div class="container" style="max-width: 560px">
            <div class="text-center mb-4">
                <a href="/" class="text-decoration-none fw-bold fs-3 text-dark">
                    Pichangas<span class="text-success">Ya</span>
                </a>
            </div>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </main>
    <script src="/vendor/global/global.min.js"></script>
    <script src="/vendor/toastr/js/toastr.min.js"></script>
    <script src="/pichanga/js/funciones.js"></script>
</body>
</html>
