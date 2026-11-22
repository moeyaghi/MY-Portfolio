<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli-server') { http_response_code(404); exit; }
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!is_string($path)) { http_response_code(400); exit; }

$routes = [
    '/' => '/index.php',
    '/contact.php' => '/contact.php',
    '/download-cv' => '/pages/download-cv.php',
    '/download-cv/' => '/pages/download-cv.php',
    '/404' => '/pages/404.php',
    '/404/' => '/pages/404.php',
    '/robots.txt' => '/robots.php',
    '/sitemap.xml' => '/sitemap.php',
];
if (isset($routes[$path])) { require __DIR__ . $routes[$path]; return true; }

$asset = realpath(__DIR__ . rawurldecode($path));
$assetRoot = realpath(__DIR__ . '/assets');
if (is_string($asset) && is_string($assetRoot) && str_starts_with($asset, $assetRoot . DIRECTORY_SEPARATOR) && is_file($asset) && !str_starts_with(basename($asset), '.') && strtolower(pathinfo($asset, PATHINFO_EXTENSION)) !== 'php') {
    return false;
}
http_response_code(404);
require __DIR__ . '/pages/404.php';
return true;