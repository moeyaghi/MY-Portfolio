<?php
declare(strict_types=1);

function workstation_shell_start(string $activePage, string $pageLabel, bool $homePage = false): void
{
    $sectionPrefix = $homePage ? '#' : '/#';
    ?>
<div class="app-shell">
    <header class="topbar">
        <a class="brand" href="<?= $sectionPrefix ?>overview" aria-label="Mohammad Yaghi workstation home">
            <img class="brand-mark" src="/assets/icons/my-animated-mark.svg" alt="" aria-hidden="true">
            <span class="brand-name">MOHAMMAD <span class="brand-separator">//</span> YAGHI</span>
        </a>
        <div class="topbar-path"><span class="path-dot"></span> WORKSPACE <span class="path-slash">/</span> <?= htmlspecialchars($pageLabel, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="topbar-right">
            <span class="connection"><span></span> ALL SYSTEMS NOMINAL</span>
            <button class="theme-toggle" type="button" aria-label="Toggle color theme" aria-pressed="false" title="Switch to light theme"><i class="bi bi-sun"></i><span>LIGHT UI</span></button>
            <button class="layout-toggle" type="button" aria-label="Toggle layout mode" aria-pressed="true" title="Switch to normal sizing"><i class="bi bi-aspect-ratio"></i><span>OPTIMIZED</span></button>
            <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="side-nav"><i class="bi bi-list"></i></button>
        </div>
    </header>

    <aside class="sidebar" id="side-nav">
        <div class="sidebar-label">NAVIGATION</div>
        <?php
        $sections = [
            ['overview', 'Overview', 'grid-1x2', '01'],
            ['capabilities', 'Capabilities', 'cpu', '02'],
            ['experience', 'Experience', 'diagram-3', '03'],
            ['projects', 'Projects', 'folder2-open', '04'],
            ['contact', 'Contact', 'terminal', '05'],
        ];
        foreach ($sections as [$id, $label, $icon, $number]):
            $active = $activePage === $id;
            $href = $homePage ? '#' . $id : '/#' . $id;
            ?>
            <a class="side-link<?= $active ? ' active' : '' ?>" href="<?= $href ?>"<?= $homePage ? ' data-section="' . $id . '"' : '' ?><?= $active ? ' aria-current="location"' : '' ?>>
                <span class="side-icon"><i class="bi bi-<?= $icon ?>"></i></span><span><?= $label ?></span><span class="side-key"><?= $number ?></span>
            </a>
        <?php endforeach; ?>
        <a class="side-link<?= $activePage === 'cv' ? ' active' : '' ?>" href="/download-cv"<?= $activePage === 'cv' ? ' aria-current="page"' : '' ?>>
            <span class="side-icon"><i class="bi bi-file-earmark-text"></i></span><span>CV Archive</span><span class="side-key">CV</span>
        </a>
        <div class="sidebar-spacer"></div>
        <div class="sidebar-user">
            <img src="/assets/images/profile.jpg" alt="">
            <div><strong>MOHAMMAD YAGHI</strong><span>IT_MANAGER / KSA</span></div>
            <span class="user-online" aria-label="Available"></span>
        </div>
        <div class="sidebar-foot"><span>BUILD 2026.10</span><span>WORKSTATION</span></div>
    </aside>
    <?php
}

function workstation_shell_end(string $activePage, bool $homePage = false): void
{
    $overviewActive = $activePage === 'overview';
    $capabilitiesActive = $activePage === 'capabilities';
    $experienceActive = $activePage === 'experience';
    $projectsActive = $activePage === 'projects';
    $contactActive = $activePage === 'contact';
    $cvActive = $activePage === 'cv';
    $overviewHref = $homePage ? '#overview' : '/#overview';
    $capabilitiesHref = $homePage ? '#capabilities' : '/#capabilities';
    $experienceHref = $homePage ? '#experience' : '/#experience';
    $projectsHref = $homePage ? '#projects' : '/#projects';
    $contactHref = $homePage ? '#contact' : '/#contact';
    $homeHref = $homePage ? '#overview' : '/';
    ?>
    <nav class="taskbar" aria-label="Workstation navigation">
        <a class="start-button" href="<?= $homeHref ?>" aria-label="Back to portfolio"><span class="start-mark">MY</span><span>START</span></a>
        <a class="taskbar-app<?= $overviewActive ? ' active' : '' ?>" href="<?= $overviewHref ?>"<?= $homePage ? ' data-section="overview"' : '' ?><?= $overviewActive ? ' aria-current="location"' : '' ?>><i class="bi bi-window-desktop"></i><span>profile_overview</span></a>
        <a class="taskbar-app<?= $capabilitiesActive ? ' active' : '' ?>" href="<?= $capabilitiesHref ?>"<?= $homePage ? ' data-section="capabilities"' : '' ?><?= $capabilitiesActive ? ' aria-current="location"' : '' ?>><i class="bi bi-cpu"></i><span>capabilities</span></a>
        <a class="taskbar-app<?= $experienceActive ? ' active' : '' ?>" href="<?= $experienceHref ?>"<?= $homePage ? ' data-section="experience"' : '' ?><?= $experienceActive ? ' aria-current="location"' : '' ?>><i class="bi bi-clock-history"></i><span>experience</span></a>
        <a class="taskbar-app<?= $projectsActive ? ' active' : '' ?>" href="<?= $projectsHref ?>"<?= $homePage ? ' data-section="projects"' : '' ?><?= $projectsActive ? ' aria-current="location"' : '' ?>><i class="bi bi-folder2-open"></i><span>project_archive</span></a>
        <a class="taskbar-app<?= $contactActive ? ' active' : '' ?>" href="<?= $contactHref ?>"<?= $homePage ? ' data-section="contact"' : '' ?><?= $contactActive ? ' aria-current="location"' : '' ?>><i class="bi bi-envelope"></i><span>contact</span></a>
        <a class="taskbar-app<?= $cvActive ? ' active' : '' ?>" href="/download-cv"<?= $cvActive ? ' aria-current="page"' : '' ?>><i class="bi bi-file-earmark-text"></i><span>cv_archive</span></a>
        <?php if ($homePage): ?>
            <button class="taskbar-app taskbar-terminal" type="button" id="open-contact-taskbar" aria-label="Restore contact terminal" title="Restore contact terminal" aria-expanded="false" hidden><i class="bi bi-terminal"></i><span>myaghi_terminal</span></button>
            <button class="taskbar-app taskbar-security" type="button" id="open-security-taskbar" aria-label="Restore security check" title="Restore security check" hidden><i class="bi bi-shield-check"></i><span>security_scan</span></button>
        <?php endif; ?>
        <?php if ($activePage === 'maintenance' || $activePage === 'not-found'): ?>
            <span class="taskbar-app active" aria-current="page"><i class="bi bi-<?= $activePage === 'maintenance' ? 'pause-circle' : 'exclamation-triangle' ?>"></i><span><?= $activePage === 'maintenance' ? 'maintenance_mode' : 'route_error' ?></span></span>
        <?php endif; ?>
        <div class="taskbar-right"><span class="taskbar-network"><i class="bi bi-wifi"></i></span><span class="taskbar-date"></span><span class="taskbar-clock"></span></div>
    </nav>
</div>
    <?php
}
