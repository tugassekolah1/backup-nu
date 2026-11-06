<?php

if (PHP_SAPI !== 'cli') {
    $publicPath = realpath(__DIR__.'/../public');
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $file = $publicPath === false ? null : realpath($publicPath.$path);

    if ($file !== null && str_starts_with($file, $publicPath) && is_file($file)) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        $contentTypes = [
            'css' => 'text/css; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
            'json' => 'application/json; charset=utf-8',
            'map' => 'application/json; charset=utf-8',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'txt' => 'text/plain; charset=utf-8',
        ];

        header('Content-Type: '.($contentTypes[$extension] ?? 'application/octet-stream'));
        header('Cache-Control: public, max-age=31536000, immutable');
        header('Content-Length: '.(string) filesize($file));
        readfile($file);

        return;
    }
}

require __DIR__.'/../public/index.php';
