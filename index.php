<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/workstation_shell.php';
require_once __DIR__ . '/includes/site_settings.php';

header('Vary: User-Agent', false);

if (site_setting_enabled('site.maintenance_mode')) {
  require __DIR__ . '/includes/maintenance.php';
}

if (preg_match('/^curl\//i', (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''))) {
  require __DIR__ . '/includes/terminal_portfolio.php';
  exit;
}

if (session_status() === PHP_SESSION_NONE) {
  session_name(SITE_SESSION_NAME);
  session_start();
}
if (empty($_SESSION['contact_csrf'])) {
  $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}
$contactEmail = site_setting('mail.recipient');
$copyrightText = site_setting('site.copyright_text');
$siteCaptchaEnabled = site_setting_enabled('captcha.site_enabled');
$recaptchaSettings = site_recaptcha_settings();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#101619">
  <meta name="description" content="Mohammad Yaghi is an IT Manager and Computer Network Specialist in Saudi Arabia, developing web and mobile applications and supporting infrastructure, cloud platforms, ERP, and security systems.">
  <meta name="robots" content="index, follow, max-image-preview:large">
  <?php if (SITE_URL !== ''): ?><link rel="canonical" href="<?= htmlspecialchars(SITE_URL . '/', ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Mohammad Yaghi">
  <meta property="og:title" content="Mohammad Yaghi | IT Manager &amp; Computer Network Specialist">
  <meta property="og:description" content="Web and mobile application development, IT operations, networks, cloud platforms, ERP, and security systems.">
  <?php if (SITE_URL !== ''): ?><meta property="og:url" content="<?= htmlspecialchars(SITE_URL . '/', ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:image" content="<?= htmlspecialchars(SITE_URL . '/assets/images/profile.jpg', ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
  <meta name="twitter:card" content="summary_large_image">
  <?php if (SITE_URL !== ''): ?><meta name="twitter:image" content="<?= htmlspecialchars(SITE_URL . '/assets/images/profile.jpg', ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
  <title>Mohammad Yaghi | IT Manager &amp; Computer Network Specialist</title>
  <link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg?v=20261027a">
  <link rel="icon" href="/favicon.ico?v=20261027a" sizes="any">
  <link rel="apple-touch-icon" href="assets/icons/apple-touch-icon.svg?v=20261027a">
  <link rel="stylesheet" href="assets/css/fonts.css">
  <link rel="stylesheet" href="assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/portfolio.css?v=20261122d">
  <link rel="stylesheet" href="assets/css/workstation-shell.css?v=20261027o">
  <script src="assets/js/portfolio.js?v=20261117a" defer></script>
</head>
<body data-theme="dark" data-layout="optimized">
  <a class="skip-link" href="#main-content">Skip to content</a>
  <div class="desktop-grid" aria-hidden="true"></div>
  <?php workstation_shell_start('overview', 'PROFILE_OVERVIEW', true); ?>

    <main class="workspace" id="main-content" tabindex="-1">
      <div class="workspace-heading">
        <div>
          <p class="eyebrow"><span class="eyebrow-line"></span> PERSONAL PORTFOLIO / SYS.OVERVIEW</p>
          <h1>Operator <span>Profile</span></h1>
        </div>
      </div>

      <section class="window hero-window" id="overview" aria-labelledby="profile-title">
        <div class="window-bar">
          <div class="window-app"><span class="app-glyph"><i class="bi bi-person-badge"></i></span><span>user_profile.exe</span><span class="window-tag">RUNNING</span></div>
          <div class="window-controls" aria-hidden="true"><span></span><span></span><span class="close"></span></div>
        </div>
        <div class="hero-content">
          <div class="hero-copy">
            <p class="system-label">[ SYSTEM ADMINISTRATOR // HUMAN INTERFACE ]</p>
            <h2 id="profile-title">Mohammad<br><span>Yaghi</span> <small lang="ar" dir="rtl">محمد ياغي</small></h2>
            <div class="role-line"><span class="prompt">&gt;</span> IT MANAGER <span class="role-divider">|</span> COMPUTER NETWORK SPECIALIST</div>
            <p class="hero-description">I develop web and mobile applications and build the systems people rely on, from business tools and ERP to networks, cloud platforms, and security infrastructure.</p>
            <div class="hero-actions">
              <a class="button button-primary" href="#projects"><i class="bi bi-arrow-down-right"></i> EXPLORE WORK</a>
              <a class="button button-outline" href="/download-cv"><i class="bi bi-file-earmark-arrow-down"></i> DOWNLOAD CV</a>
            </div>
            <div class="hero-footnote"><span class="footnote-mark">+</span> 150+ USERS SUPPORTED <span class="footnote-divider">/</span> 6-COMPANY ERP OPERATIONS</div>
          </div>
          <div class="profile-frame">
            <div class="frame-index">FIG. 01 <span>IDENTITY PROFILE</span></div>
            <div class="profile-photo-wrap"><img src="assets/images/profile.jpg" alt="Portrait of Mohammad Yaghi"></div>
            <div class="profile-readout">
              <div><span>ROLE</span><strong>IT MANAGER</strong></div>
              <div><span>LANGUAGES</span><strong>AR / EN</strong></div>
              <div class="profile-location"><span>LOCATION</span><strong>JEDDAH, SAUDI ARABIA</strong></div>
            </div>
            <div class="frame-stamp"><i class="bi bi-shield-check"></i> SCE · COMPUTER NETWORK SPECIALIST</div>
          </div>
        </div>
        <div class="window-status"><span><i class="bi bi-circle-fill"></i> PROFILE MODULE</span><span>SYNCED <b>100%</b></span><span>ACCESS: PUBLIC</span></div>
      </section>

      <section class="metrics-grid" aria-label="Operational metrics">
        <article class="metric-window">
          <div class="metric-head"><span>01 / USERS</span><i class="bi bi-people"></i></div>
          <div class="metric-value">150<span>+</span></div>
          <div class="metric-label">EMPLOYEES SUPPORTED</div>
          <div class="metric-track" aria-hidden="true"><span style="width:76%"></span></div>
        </article>
        <article class="metric-window">
          <div class="metric-head"><span>02 / CLOUD</span><i class="bi bi-cloud-check"></i></div>
          <div class="metric-value">300</div>
          <div class="metric-label">M365 + WORKSPACE ACCOUNTS</div>
          <div class="metric-track amber" aria-hidden="true"><span style="width:62%"></span></div>
        </article>
        <article class="metric-window">
          <div class="metric-head"><span>03 / PLATFORMS</span><i class="bi bi-globe2"></i></div>
          <div class="metric-value">07</div>
          <div class="metric-label">DOMAINS &amp; DIGITAL PLATFORMS</div>
          <div class="metric-track blue" aria-hidden="true"><span style="width:48%"></span></div>
        </article>
        <article class="metric-window">
          <div class="metric-head"><span>04 / ERP</span><i class="bi bi-diagram-3"></i></div>
          <div class="metric-value">06</div>
          <div class="metric-label">COMPANIES ON ERP OPERATIONS</div>
          <div class="metric-track" aria-hidden="true"><span style="width:85%"></span></div>
        </article>
      </section>

      <section class="window capabilities-window" id="capabilities">
        <div class="window-bar">
          <div class="window-app"><span class="app-glyph green"><i class="bi bi-cpu"></i></span><span>capability_matrix.sys</span></div>
          <div class="window-caption">CORE MODULES <span>06</span></div>
          <div class="window-controls" aria-hidden="true"><span></span><span></span><span class="close"></span></div>
        </div>
        <div class="section-intro"><div><p class="system-label">[ INSTALLED MODULES ]</p><h2>Technical <span>stack</span></h2></div><span class="section-code">MATRIX / 001-A</span></div>
        <div class="module-grid">
          <article class="module-card"><span class="module-number">MOD_01</span><i class="bi bi-headset"></i><h3>IT OPERATIONS</h3><p>End-user support, device management, documentation, and business-critical support.</p><div class="module-tags"><span>SUPPORT</span><span>OPERATIONS</span></div></article>
          <article class="module-card"><span class="module-number">MOD_02</span><i class="bi bi-router"></i><h3>NETWORKS &amp; SYSTEMS</h3><p>Windows Server, Active Directory, LAN/VLAN/VPN, routing, switching, and Linux.</p><div class="module-tags"><span>NETWORK</span><span>INFRA</span></div></article>
          <article class="module-card"><span class="module-number">MOD_03</span><i class="bi bi-cloud"></i><h3>CLOUD &amp; PLATFORMS</h3><p>Microsoft 365, Google Workspace, Odoo ERP, AWS EC2, hosting, DNS, SSL, and migrations.</p><div class="module-tags"><span>CLOUD</span><span>ERP</span></div></article>
          <article class="module-card"><span class="module-number">MOD_04</span><i class="bi bi-window-stack"></i><h3>SOFTWARE DEVELOPMENT</h3><p>Web and mobile applications, business systems, databases, and integrations. Technologies include Python, JavaScript, PHP, C++, C#, Kotlin, Flutter, and MySQL.</p><div class="module-tags"><span>WEB</span><span>MOBILE</span></div></article>
          <article class="module-card"><span class="module-number">MOD_05</span><i class="bi bi-camera-video"></i><h3>SECURITY SYSTEMS</h3><p>CCTV, NVR, access control, PBX/IP telephony, and low-current infrastructure.</p><div class="module-tags"><span>CCTV</span><span>ACCESS</span></div></article>
          <article class="module-card"><span class="module-number">MOD_06</span><i class="bi bi-file-earmark-lock"></i><h3>SECURITY PRACTICES</h3><p>Access control, user awareness, policy material, and compliance support.</p><div class="module-tags"><span>POLICY</span><span>RISK</span></div></article>
        </div>
      </section>

      <section class="window experience-window" id="experience">
        <div class="window-bar">
          <div class="window-app"><span class="app-glyph amber"><i class="bi bi-clock-history"></i></span><span>career_timeline.log</span></div>
          <div class="window-caption">HISTORY <span>2019—NOW</span></div>
          <div class="window-controls" aria-hidden="true"><span></span><span></span><span class="close"></span></div>
        </div>
        <div class="section-intro"><div><p class="system-label">[ CAREER LOG ]</p><h2>Experience <span>timeline</span></h2></div><span class="section-code">3 RECORDS</span></div>
        <div class="timeline">
          <article class="timeline-entry current"><div class="timeline-date">JUL 2023 — NOW</div><div class="timeline-marker"></div><div class="timeline-detail"><h3>IT Manager</h3><p>Teeb Al Hayah Commercial Company (THCC) <span>· Jeddah, Saudi Arabia</span></p><small>Promoted after taking on IT responsibilities and building increasingly capable internal tools.</small></div><span class="timeline-state">CURRENT</span></article>
          <article class="timeline-entry"><div class="timeline-date">FEB 2022 — JUL 2023</div><div class="timeline-marker"></div><div class="timeline-detail"><h3>Data Entry with IT Responsibilities</h3><p>Teeb Al Hayah Commercial Company (THCC) <span>· Jeddah, Saudi Arabia</span></p><small>Expanded into IT support and internal tools while continuing to develop technical skills.</small></div><span class="timeline-state">THCC</span></article>
          <article class="timeline-entry"><div class="timeline-date">2019 — 2022</div><div class="timeline-marker"></div><div class="timeline-detail"><h3>Independent Learning &amp; Job Search</h3><p>Self-directed development <span>· Jordan</span></p><small>HTML, CSS, PHP, MySQL; CCNA and Advanced PHP coursework.</small></div><span class="timeline-state">FOUNDATION</span></article>
        </div>
      </section>

      <section class="window projects-window" id="projects">
        <div class="window-bar">
          <div class="window-app"><span class="app-glyph blue"><i class="bi bi-folder2-open"></i></span><span>project_archive.dir</span></div>
          <div class="window-caption">SELECTED WORK <span>2022—2025</span></div>
          <div class="window-controls" aria-hidden="true"><span></span><span></span><span class="close"></span></div>
        </div>
        <div class="section-intro project-intro"><div><p class="system-label">[ DEPLOYED SYSTEMS &amp; INITIATIVES ]</p><h2>Project <span>archive</span></h2></div><div class="filter-group" role="group" aria-label="Filter projects"><button class="filter-button active" type="button" data-filter="all" aria-pressed="true">ALL</button><button class="filter-button" type="button" data-filter="systems" aria-pressed="false">SYSTEMS</button><button class="filter-button" type="button" data-filter="infra" aria-pressed="false">INFRA</button><button class="filter-button" type="button" data-filter="security" aria-pressed="false">SECURITY</button></div></div>
        <div class="project-list">
          <article class="project-row" data-category="systems"><span class="project-id">PRJ_01</span><div class="project-symbol"><i class="bi bi-diagram-3"></i></div><div class="project-info"><h3>Odoo.sh Enterprise ERP Consolidation</h3><p>ERP administration and workflow coordination across 6 companies and multiple departments.</p></div><span class="project-type">ERP / SYSTEMS</span><span class="project-arrow"><i class="bi bi-arrow-up-right"></i></span></article>
          <article class="project-row" data-category="systems"><span class="project-id">PRJ_02</span><div class="project-symbol amber"><i class="bi bi-upc-scan"></i></div><div class="project-info"><h3>Haram Smart Charging &amp; Safe-Keeping</h3><p>Originated the client solution and led its delivery, including supplier coordination and Ramadan operation across 16 areas.</p></div><span class="project-type">SYSTEMS / OPERATIONS</span><span class="project-arrow"><i class="bi bi-arrow-up-right"></i></span></article>
          <article class="project-row" data-category="security infra"><span class="project-id">PRJ_03</span><div class="project-symbol blue"><i class="bi bi-camera-video"></i></div><div class="project-info"><h3>Holy Sites Mosque Security Systems</h3><p>Supported delivery of CCTV, cabling, monitoring-room connectivity, configuration, and readiness for four locations.</p></div><span class="project-type">SECURITY / INFRA</span><span class="project-arrow"><i class="bi bi-arrow-up-right"></i></span></article>
          <article class="project-row" data-category="systems"><span class="project-id">PRJ_04</span><div class="project-symbol"><i class="bi bi-receipt"></i></div><div class="project-info"><h3>ZATCA Invoicing System</h3><p>Developed an internal invoicing system for a newly separated environmental consulting company.</p></div><span class="project-type">BUSINESS / SYSTEMS</span><span class="project-arrow"><i class="bi bi-arrow-up-right"></i></span></article>
          <article class="project-row" data-category="infra"><span class="project-id">PRJ_05</span><div class="project-symbol amber"><i class="bi bi-hdd-network"></i></div><div class="project-info"><h3>Websites, Hosting &amp; Digital Platforms</h3><p>Managed company websites, domains, DNS, SSL, email, hosting, migrations, and platform integrations.</p></div><span class="project-type">INFRA / CLOUD</span><span class="project-arrow"><i class="bi bi-arrow-up-right"></i></span></article>
        </div>
        <div class="archive-footer"><span><i class="bi bi-folder-check"></i> 05 RECORDS SHOWN</span><span>ARCHIVE STATUS: <b>INDEXED</b></span></div>
      </section>

      <section class="contact-window" id="contact">
        <div class="contact-top"><span class="contact-kicker">[ OPEN CHANNEL ]</span><span class="contact-status"><i class="bi bi-circle-fill"></i> AVAILABLE FOR THE RIGHT OPPORTUNITY</span></div>
        <div class="contact-main">
          <div><h2>Let's solve<br><span>something useful.</span></h2><p>For IT operations, systems, infrastructure, or technical project conversations.</p></div>
          <div class="contact-actions">
            <button class="button button-primary contact-launch" type="button" id="open-contact-terminal"><i class="bi bi-terminal"></i> CONTACT <i class="bi bi-arrow-up-right"></i></button>
            <div class="social-links" role="group" aria-label="Social profiles">
              <a class="social-link linkedin-link" href="https://www.linkedin.com/in/myaghixyz/" target="_blank" rel="noopener">
                <i class="bi bi-linkedin" aria-hidden="true"></i><span><strong>LINKEDIN</strong><small>PROFESSIONAL PROFILE</small></span><i class="bi bi-arrow-up-right social-arrow" aria-hidden="true"></i>
              </a>
              <a class="social-link github-link" href="https://github.com/moeyaghi" target="_blank" rel="noopener">
                <i class="bi bi-github" aria-hidden="true"></i><span><strong>GITHUB</strong><small>PROJECTS &amp; CODE</small></span><i class="bi bi-arrow-up-right social-arrow" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="contact-bottom"><span>MOHAMMAD YAGHI <span class="role-divider">/</span> IT MANAGER</span><span>JEDDAH, SAUDI ARABIA</span></div>
      </section>
      <dialog class="terminal-dialog" id="contact-terminal" aria-labelledby="contact-terminal-title">
        <section class="terminal-window">
          <header class="terminal-titlebar">
            <div class="terminal-title"><i class="bi bi-terminal"></i><span id="contact-terminal-title">MYAGHI OS :: Terminal</span></div>
            <div class="desktop-window-controls">
              <button class="desktop-window-control" type="button" data-minimize-window="contact" aria-label="Minimize contact terminal" title="Minimize"><i class="bi bi-dash-lg"></i></button>
              <button class="desktop-window-control" type="button" data-maximize-window="contact" aria-label="Maximize contact terminal" title="Maximize"><i class="bi bi-square"></i></button>
              <button class="desktop-window-control close" type="button" id="close-contact-terminal" aria-label="Close contact terminal" title="Close"><i class="bi bi-x-lg"></i></button>
            </div>
          </header>
          <div class="terminal-content">
            <div class="terminal-output" id="terminal-output" role="log" aria-live="polite" aria-relevant="additions">
              <p>MYAGHI OS [Version 1.0]</p>
              <p>(c) 2026 Mohammad Yaghi. All rights reserved.</p>
              <p class="terminal-hint">Boot sequence complete. Opening contact channel...</p>
              <?php if ($contactEmail !== ''): ?><p>CONTACT: <a href="mailto:<?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8') ?></a></p><?php else: ?><p>CONTACT: EMAIL NOT CONFIGURED</p><?php endif; ?>
              <p class="terminal-hint">Type HELP at any prompt for available commands.</p>
            </div>
            <form class="terminal-form" id="contact-form" action="/contact.php" method="post" data-email="<?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8') ?>" data-captcha-enabled="<?= $siteCaptchaEnabled ? '1' : '0' ?>">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['contact_csrf'], ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="name" value="">
              <input type="hidden" name="email" value="">
              <input type="hidden" name="message" value="">
              <input type="hidden" name="g-recaptcha-response" id="contact-captcha-token" value="">
              <div class="terminal-honeypot" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
              <label class="terminal-command" for="terminal-entry"><span id="terminal-prompt">C:\Users\visitor&gt; set /p NAME=Name:</span><textarea class="terminal-entry" id="terminal-entry" rows="1" maxlength="120" autocomplete="name" aria-label="Your name" autofocus></textarea></label>
            </form>
          </div>
        </section>
      </dialog>
      <dialog class="security-dialog" id="contact-security" aria-labelledby="contact-security-title">
        <section class="security-window">
          <header class="security-titlebar">
            <div class="security-title"><i class="bi bi-shield-check" aria-hidden="true"></i><span id="contact-security-title">MYAGHI SECURITY CENTER</span></div>
            <span class="security-state">MESSAGE SCAN</span>
            <div class="desktop-window-controls">
              <button class="desktop-window-control" type="button" data-minimize-window="security" aria-label="Minimize security check" title="Minimize"><i class="bi bi-dash-lg"></i></button>
              <button class="desktop-window-control" type="button" data-maximize-window="security" aria-label="Maximize security check" title="Maximize"><i class="bi bi-square"></i></button>
              <button class="desktop-window-control close" type="button" id="cancel-contact-security" aria-label="Close security check" title="Close"><i class="bi bi-x-lg"></i></button>
            </div>
          </header>
          <div class="security-content">
            <p class="security-heading">Verify this session</p>
            <p class="security-copy">Complete the human check before MYAGHI OS releases this message.</p>
            <?php if ($siteCaptchaEnabled): ?><div class="security-captcha"><div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptchaSettings['site_key'], ENT_QUOTES, 'UTF-8') ?>" data-action="CONTACT_MESSAGE" data-callback="onContactCaptchaVerified" data-expired-callback="onContactCaptchaExpired" data-error-callback="onContactCaptchaError"></div></div><?php endif; ?>
            <p class="security-status" id="contact-captcha-status" role="status" aria-live="polite">Waiting for security check...</p>
            <button class="security-retry" type="button" id="retry-contact-captcha">RETRY CHECK</button>
            <button class="security-cancel" type="button" id="security-cancel-action">CANCEL CHECK</button>
          </div>
        </section>
      </dialog>
      <footer class="workspace-footer"><span>MOHAMMAD YAGHI <span class="role-divider">/</span> PORTFOLIO</span><span>BUILT FOR THE REAL WORLD <b>■</b></span><span><?= htmlspecialchars($copyrightText, ENT_QUOTES, 'UTF-8') ?></span></footer>
    </main>

  <?php workstation_shell_end('overview', true); ?>
</body>
</html>
