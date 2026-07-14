<?php

header('X-Security-Scan-Server: enabled');

$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$publicPath = realpath(__DIR__.'/../../public');
$filePath = realpath($publicPath.$uri);

// PHP's development server bypasses Laravel for existing static files. These
// headers emulate the production Apache configuration in public/.htaccess.
if ($uri !== '/' && $filePath && str_starts_with($filePath, $publicPath) && is_file($filePath)) {
    $mimeTypes = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    header('Content-Type: '.($mimeTypes[$extension] ?? 'application/octet-stream'));
    header('Content-Length: '.filesize($filePath));
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');

    readfile($filePath);

    return true;
}

require $publicPath.'/index.php';
