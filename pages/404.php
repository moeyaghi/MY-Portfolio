<?php
declare(strict_types=1);
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/workstation_shell.php';
require_once __DIR__ . '/../includes/site_settings.php';
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mohammad Yaghi</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="/assets/css/fonts.css">
  <link rel="stylesheet" href="/assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/css/workstation-shell.css?v=20261027o">
  <link rel="stylesheet" href="/assets/css/maintenance.css?v=20261027e">
  <script src="/assets/js/workstation-shell.js?v=20261027e" defer></script>
</head>
<body data-theme="dark" data-layout="optimized">
  <div class="desktop-grid" aria-hidden="true"></div>
  <?php workstation_shell_start('not-found', 'ROUTE_NOT_FOUND'); ?>
  <main class="maintenance-main">
    <section class="maintenance-window">
      <div class="maintenance-titlebar"><i class="bi bi-exclamation-triangle"></i><span>route_not_found.err</span></div>
      <div class="maintenance-content">
        <p class="maintenance-eyebrow">[ REQUEST UNAVAILABLE ]</p>
        <h1>Page <span>not found</span></h1>
        <p>The page you requested does not exist or is not available from this address.</p>
        <a class="maintenance-home" href="/">RETURN TO PORTFOLIO <i class="bi bi-arrow-up-right"></i></a>
      </div>
    </section>
  </main>
  <footer class="maintenance-footer"><?= htmlspecialchars(site_setting('site.copyright_text'), ENT_QUOTES, 'UTF-8') ?></footer>
  <?php workstation_shell_end('not-found'); ?>
</body>
</html>