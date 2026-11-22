<?php
declare(strict_types=1);
/**
 * Maintenance mode page.
 * Include this file and exit — do not redirect, to avoid routing dependencies.
 */
http_response_code(503);
header('Retry-After: 3600');
require_once __DIR__ . '/workstation_shell.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maintenance — Mohammad Yaghi</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="/assets/css/fonts.css">
  <link rel="stylesheet" href="/assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/css/workstation-shell.css?v=20261027o">
  <link rel="stylesheet" href="/assets/css/maintenance.css?v=20261027e">
  <script src="/assets/js/workstation-shell.js?v=20261027e" defer></script>
</head>
<body class="is-ready" data-theme="dark" data-layout="optimized">
  <div class="desktop-grid" aria-hidden="true"></div>
  <?php workstation_shell_start('maintenance', 'MAINTENANCE_MODE'); ?>
  <main class="maintenance-main">
    <section class="maintenance-window">
      <div class="maintenance-titlebar"><i class="bi bi-cpu"></i><span>maintenance_mode.sys</span></div>
      <div class="maintenance-content">
        <p class="maintenance-eyebrow">[ TEMPORARILY OFFLINE ]</p>
        <h1>Under <span>maintenance</span></h1>
        <p>The site is undergoing scheduled maintenance. Please check back soon.</p>
        <div class="maintenance-progress"><span></span> SERVICE PAUSED</div>
      </div>
    </section>
  </main>
  <footer class="maintenance-footer"><?= htmlspecialchars(site_setting('site.copyright_text'), ENT_QUOTES, 'UTF-8') ?></footer>
  <?php workstation_shell_end('maintenance'); ?>
</body>
</html>
<?php exit; ?>
