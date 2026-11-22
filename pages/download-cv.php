<?php
declare(strict_types=1);
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/recaptcha_helpers.php';
require __DIR__ . '/../includes/workstation_shell.php';
require __DIR__ . '/../includes/cv_template.php';
require_once __DIR__ . '/../includes/site_settings.php';

session_name(SITE_SESSION_NAME);
session_start();

if (site_setting_enabled('site.maintenance_mode')) {
  require __DIR__ . '/../includes/maintenance.php';
}

function download_verified(): bool {
  if (!site_setting_enabled('captcha.download_enabled')) { return true; }
    return !empty($_SESSION['download_verified_until']) && (int)$_SESSION['download_verified_until'] > time();
}

function render_cv_template_html(): string {
  return cv_template_html(site_current_origin() . '/');
}

$error       = '';
$show_preview = download_verified();
$recaptchaSettings = site_recaptcha_settings();

if (isset($_GET['preview'])) {
  if (!download_verified()) {
    header('Location: /download-cv');
    exit;
  }
  header('Content-Type: text/html; charset=UTF-8');
  header('Cache-Control: private, no-store, no-cache, must-revalidate');
  echo render_cv_template_html();
  exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!site_setting_enabled('captcha.download_enabled')) {
    $_SESSION['download_verified_until'] = time() + DOWNLOAD_VERIFICATION_SECONDS;
    header('Location: /download-cv');
    exit;
  }
    $result = verify_recaptcha_enterprise((string)($_POST['g-recaptcha-response'] ?? ''), 'DOWNLOAD_CV');
    if ($result['success']) {
        $_SESSION['download_verified_until'] = time() + DOWNLOAD_VERIFICATION_SECONDS;
      header('Location: /download-cv');
      exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mohammad Yaghi</title>
  <meta name="robots" content="noindex, nofollow">
  <script>document.documentElement.classList.add('js');</script>
  <link rel="stylesheet" href="/assets/css/fonts.css">
  <link rel="stylesheet" href="/assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
  <?php if (!$show_preview && site_setting_enabled('captcha.download_enabled') && !recaptcha_bypass_enabled()): ?>
  <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
  <?php endif; ?>
  <link rel="stylesheet" href="/assets/css/download-cv.css?v=20261027l">
  <link rel="stylesheet" href="/assets/css/workstation-shell.css?v=20261027o">
  <script src="/assets/js/workstation-shell.js?v=20261027f" defer></script>
</head>

<?php if ($show_preview): ?>
<body class="preview is-loading" data-theme="dark" data-layout="optimized">
  <div class="desktop-grid" aria-hidden="true"></div>
  <?php workstation_shell_start('cv', 'CV_ARCHIVE'); ?>
  <div class="page-loader" id="page-loader" aria-hidden="true">
    <div class="page-loader__spinner"></div>
    <div class="page-loader__label">Preparing CV</div>
  </div>

  <main class="cv-page-main cv-page-main--preview">
    <div class="cv-stage">
      <div class="cv-frame-shell">
        <div class="cv-viewer" aria-label="CV document viewer">
          <div class="cv-viewer-titlebar">
            <div class="cv-viewer-document">
              <i class="bi bi-file-earmark-pdf cv-viewer-file-icon" aria-hidden="true"></i>
              <span class="cv-viewer-filename">Mohammad_Yaghi_CV.pdf</span>
              <span class="cv-viewer-doc-tag">PDF DOCUMENT</span>
            </div>
            <div class="cv-viewer-window-controls" aria-hidden="true"><i class="bi bi-dash-lg"></i><i class="bi bi-square"></i><i class="bi bi-x-lg"></i></div>
          </div>
          <div class="cv-viewer-toolbar">
            <div class="cv-viewer-tools" aria-hidden="true">
              <span class="cv-viewer-tool active"><i class="bi bi-cursor"></i></span>
              <span class="cv-viewer-tool"><i class="bi bi-hand-index"></i></span>
              <span class="cv-viewer-divider"></span>
              <span class="cv-viewer-tool"><i class="bi bi-search"></i></span>
              <span class="cv-viewer-tool"><i class="bi bi-arrows-fullscreen"></i></span>
            </div>
            <span class="cv-viewer-page-count" id="cv-page-count">PAGE 1 / 1</span>
            <span class="cv-viewer-zoom" id="cv-zoom-level">100%</span>
            <button class="btn-act primary cv-viewer-print" type="button" onclick="downloadCV()" title="Print CV"><i class="bi bi-printer"></i><span class="btn-text">Print</span></button>
          </div>
        </div>
        <div class="cv-frame-wrap">
          <div class="cv-frame-viewport">
            <div class="cv-frame-scaler" id="cv-frame-scaler">
              <iframe id="cv-frame" src="/download-cv?preview=1" title="CV Preview" scrolling="no"></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <footer class="footer">
    <div class="container">
      <?= htmlspecialchars(site_setting('site.copyright_text'), ENT_QUOTES, 'UTF-8') ?>
    </div>
  </footer>
  <script src="/assets/js/download-cv-preview.js?v=20261027g"></script>
  <?php workstation_shell_end('cv'); ?>
</body>

<?php else: ?>
<body class="gate is-loading" data-theme="dark" data-layout="optimized">
  <div class="desktop-grid" aria-hidden="true"></div>
  <?php workstation_shell_start('cv', 'CV_ARCHIVE'); ?>
  <div class="page-loader" id="page-loader" aria-hidden="true">
    <div class="page-loader__spinner"></div>
    <div class="page-loader__label">Loading</div>
  </div>

  <main class="cv-page-main cv-page-main--gate">
    <form class="box" method="post" autocomplete="off">
      <img class="mark" src="assets/icons/my-animated-mark.svg" alt="" aria-hidden="true">
      <h1>Preview CV</h1>
      <p>Confirm you are human to view and download the CV.</p>
      <div class="loading"><span></span></div>
      <?php if (site_setting_enabled('captcha.download_enabled') && !recaptcha_bypass_enabled()): ?>
      <div class="captcha">
        <div class="g-recaptcha" data-sitekey="<?= e($recaptchaSettings['site_key']) ?>" data-action="DOWNLOAD_CV" data-callback="onCaptchaVerified"></div>
      </div>
      <?php endif; ?>
      <a class="gate-link" href="/">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Portfolio
      </a>
      <?php if ($error !== ''): ?>
      <div class="error"><?= e($error) ?></div>
      <?php endif; ?>
    </form>
  </main>
  <footer class="footer">
    <div class="container">
      <?= htmlspecialchars(site_setting('site.copyright_text'), ENT_QUOTES, 'UTF-8') ?>
    </div>
  </footer>
  <script>window.RECAPTCHA_BYPASS = <?= json_encode(recaptcha_bypass_enabled()) ?>;</script>
  <script src="/assets/js/download-cv-gate.js?v=20261027f"></script>
  <?php workstation_shell_end('cv'); ?>
</body>
<?php endif; ?>
</html>
