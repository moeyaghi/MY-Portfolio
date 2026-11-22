<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';

header('Content-Type: text/plain; charset=UTF-8');
echo "User-agent: *\nAllow: /\nDisallow: /includes/\nDisallow: /vendor/\nDisallow: /var/\n";
if (SITE_URL !== '') { echo "\nSitemap: " . SITE_URL . "/sitemap.xml\n"; }