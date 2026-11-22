<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

$color = ($_GET['color'] ?? '') !== 'never';
$reset = $color ? "\033[0m" : '';
$accent = $color ? "\033[1;36m" : '';
$green = $color ? "\033[1;32m" : '';
$base = site_current_origin();

$banner = <<<'ART'
    __  ___      __                                        __   __  __            __    _ 
   /  |/  /___  / /_  ____ _____ ___  ____ ___  ____ _____/ /   \ \/ /___ _____ _/ /_  (_)
  / /|_/ / __ \/ __ \/ __ `/ __ `__ \/ __ `__ \/ __ `/ __  /     \  / __ `/ __ `/ __ \/ / 
 / /  / / /_/ / / / / /_/ / / / / / / / / / / / /_/ / /_/ /      / / /_/ / /_/ / / / / /  
/_/  /_/\____/_/ /_/\__,_/_/ /_/ /_/_/ /_/ /_/\__,_/\__,_/      /_/\__,_/\__, /_/ /_/_/   
                                                                        /____/            
ART;

$lines = explode("\n", $banner);
$animate = $color && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET';
$frameCount = $animate ? 24 : 1;

if ($animate) {
    header('X-Accel-Buffering: no');
    if (function_exists('apache_setenv')) { apache_setenv('no-gzip', '1'); }
    while (ob_get_level() > 0 && (ob_get_status()['flags'] & PHP_OUTPUT_HANDLER_REMOVABLE)) {
        ob_end_flush();
    }
    echo "\033[2J\033[H\033[s";
}

$renderBanner = static function (int $frame, bool $final = false) use ($lines, $color, $animate, $reset): void {
    $finalColors = [[255, 75, 75], [255, 160, 45], [255, 230, 65], [70, 220, 125], [65, 200, 255], [205, 105, 255]];
    foreach ($lines as $index => $line) {
        if ($color && $final) {
            [$red, $greenChannel, $blue] = $finalColors[$index % count($finalColors)];
            echo "\033[38;2;{$red};{$greenChannel};{$blue}m" . $line . $reset . "\n";
        } elseif ($color) {
            $visible = $animate ? min(strlen($line), ($frame + 1) * 8) : strlen($line);
            for ($column = 0; $column < $visible; $column += 3) {
                $chunk = substr($line, $column, min(3, $visible - $column));
                $phase = $column / 13 - $frame / 3 + $index * 0.24;
                $brightness = 0.8 + 0.2 * sin($frame * 0.45);
                $red = (int) ((95 + 160 * (0.5 + 0.5 * sin($phase))) * $brightness);
                $greenChannel = (int) ((95 + 160 * (0.5 + 0.5 * sin($phase + 2.094))) * $brightness);
                $blue = (int) ((95 + 160 * (0.5 + 0.5 * sin($phase + 4.189))) * $brightness);
                if ($animate && abs($column - (($frame * 5) % 96)) < 3) { $red = 255; $greenChannel = 255; $blue = 255; }
                echo "\033[38;2;{$red};{$greenChannel};{$blue}m" . $chunk;
            }
            echo $reset . "\n";
        } else {
            echo $line . "\n";
        }
    }
};

for ($frame = 0; $frame < $frameCount; $frame++) {
    if ($animate && $frame > 0) { echo "\033[u\033[J"; }
    $renderBanner($frame);
    if ($animate) {
        flush();
        if (connection_aborted()) { return; }
        if ($frame < $frameCount - 1) { usleep(120000); }
    }
}

$sections = [
    'ABOUT' => "Mohammad Yaghi\nIT Manager | Computer Network Specialist\nJeddah, Saudi Arabia | Arabic / English\n\nI develop web and mobile applications and business tools, and manage\nnetworks, ERP, cloud platforms, and security infrastructure.",
    'AT A GLANCE' => "150+ employees supported | 300 Microsoft 365 and Workspace accounts\n7 domains and digital platforms | ERP operations across 6 companies",
    'EXPERIENCE' => "Jul 2023 - present  IT Manager, Teeb Al Hayah Commercial Company, Jeddah\n  IT operations, networks, cloud platforms, ERP, and internal systems\nFeb 2022 - Jul 2023  Data Entry with IT Responsibilities, THCC, Jeddah\n  IT support and development of internal tools\n2019 - 2022          Independent learning and job search, Jordan",
    'EDUCATION' => "Bachelor's Degree in Computer Networks\nJerash University, Jordan | 2019\nSaudi Council of Engineers: Specialist - Computer Network",
    'CERTIFICATIONS AND TRAINING' => "Cybersecurity Training Diploma (300 hours) | 2024 - 2025\nIntroduction to Network Engineering | 2025\nCSCU Certified Secure Computer User | 2024 - 2025\nCompTIA A+ Course | 2024\nDigital Literacy | 2024\nAdvanced PHP Web Development Course | 2021\nCCNA Course | 2019",
    'TECHNICAL STACK' => "IT operations     End-user support, device management, documentation\nNetworks          Windows Server, Active Directory, LAN, VLAN, VPN, Linux\nCloud             Microsoft 365, Google Workspace, AWS EC2, DNS, SSL\nBusiness systems  Odoo ERP administration and workflow tools\nDevelopment       Web and mobile apps, business systems, databases, APIs\n                  Python, JavaScript, PHP, C++, C#, Kotlin, Flutter, MySQL,\n                  HTML and CSS, among other technologies\nSecurity systems  CCTV, NVR, access control, PBX and IP telephony\nSecurity practice Access control, user awareness, policy and compliance support",
    'SELECTED PROJECTS' => "01  Odoo.sh Enterprise ERP administration across six companies\n02  Haram smart charging and safe-keeping across 16 areas\n03  Holy Sites mosque security systems across four locations\n04  ZATCA invoicing system and field survey image processor\n05  Internal operations tools and digital platforms",
    'CONTACT' => 'Email     ' . (site_setting('mail.recipient') ?: 'Not configured') . "\nLinkedIn  https://www.linkedin.com/in/myaghixyz/\nGitHub    https://github.com/moeyaghi",
    'CV' => "Full CV: {$base}/download-cv\nThe full CV is available through the website's verification process.",
];

$portfolioGroups = [['', $green . '  HELLO & WELCOME' . $reset, '']];
foreach ($sections as $title => $content) {
    $portfolioGroups[] = array_merge([$accent . '[ ' . $title . ' ]' . $reset], explode("\n", $content), ['']);
}
if ($animate) {
    echo "\033[u\033[J";
    $renderBanner($frame, true);
}

foreach ($portfolioGroups as $group) {
    echo implode("\n", $group) . "\n";
}
