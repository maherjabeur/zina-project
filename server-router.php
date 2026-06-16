<?php

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__.'/public'.$path;

if (is_file($file)) {
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $contentTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
    ];

    if (isset($contentTypes[$extension])) {
        header('Content-Type: '.$contentTypes[$extension]);
    }

    header('Content-Length: '.filesize($file));
    readfile($file);

    return true;
}

$_SERVER['SCRIPT_FILENAME'] = __DIR__.'/public/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__.'/public/index.php';
