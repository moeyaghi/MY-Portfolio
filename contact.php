<?php
declare(strict_types=1);

require __DIR__ . '/includes/config.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/includes/recaptcha_helpers.php';
require_once __DIR__ . '/includes/site_settings.php';

use PHPMailer\PHPMailer\PHPMailer;

function contact_response(int $status, array $data): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
    exit;
}

function contact_rate_limit_allows(string $ip): bool
{
    $directory = __DIR__ . '/var/contact';
    if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
        return false;
    }

    $handle = @fopen($directory . '/rate-limit.json', 'c+');
    if ($handle === false || !flock($handle, LOCK_EX)) {
        if (is_resource($handle)) { fclose($handle); }
        return false;
    }

    $state = json_decode((string)stream_get_contents($handle), true);
    if (!is_array($state)) { $state = []; }
    $now = time();
    foreach ($state as $key => $entry) {
        if (!is_array($entry) || (int)($entry['window'] ?? 0) <= $now - 3600) {
            unset($state[$key]);
        }
    }

    $key = hash('sha256', 'contact-send-v2|' . $ip);
    $entry = $state[$key] ?? ['window' => $now, 'count' => 0];
    if ((int)$entry['count'] >= 5) {
        flock($handle, LOCK_UN);
        fclose($handle);
        return false;
    }

    $entry['count'] = (int)$entry['count'] + 1;
    $state[$key] = $entry;
    rewind($handle);
    ftruncate($handle, 0);
    fwrite($handle, json_encode($state, JSON_UNESCAPED_SLASHES));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
    return true;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    contact_response(405, ['ok' => false, 'message' => 'POST required.']);
}

session_name(SITE_SESSION_NAME);
session_start();

$csrf = (string) ($_POST['csrf'] ?? '');
if (empty($_SESSION['contact_csrf']) || !hash_equals((string) $_SESSION['contact_csrf'], $csrf)) {
    contact_response(403, ['ok' => false, 'message' => 'Session expired. Reload the page and try again.']);
}

if (trim((string) ($_POST['website'] ?? '')) !== '') {
    contact_response(200, ['ok' => true, 'message' => 'Message received.']);
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || strlen($name) > 120) {
    contact_response(422, ['ok' => false, 'message' => 'Enter a name between 1 and 120 characters.']);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
    contact_response(422, ['ok' => false, 'message' => 'Enter a valid email address.']);
}
if ($message === '' || strlen($message) > 5000) {
    contact_response(422, ['ok' => false, 'message' => 'Enter a message up to 5,000 characters.']);
}

if (site_setting_enabled('captcha.site_enabled')) {
    $captcha = verify_recaptcha_enterprise((string) ($_POST['g-recaptcha-response'] ?? ''), 'CONTACT_MESSAGE');
    if (!$captcha['success']) {
        contact_response(422, [
            'ok' => false,
            'captchaRequired' => true,
            'message' => 'Security check failed. Please verify again.',
        ]);
    }
}

$mail = site_mail_settings();
$smtpReady = $mail['enabled']
    && $mail['host'] !== ''
    && $mail['recipient'] !== ''
    && $mail['from_email'] !== ''
    && $mail['from_name'] !== ''
    && $mail['username'] !== ''
    && $mail['password_configured']
    && filter_var($mail['recipient'], FILTER_VALIDATE_EMAIL)
    && filter_var($mail['from_email'], FILTER_VALIDATE_EMAIL)
    && filter_var($mail['port'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]) !== false
    && in_array($mail['encryption'], ['tls', 'ssl'], true);

if (!$smtpReady) {
    contact_response(503, [
        'ok' => false,
        'configurationRequired' => true,
        'message' => 'Mail delivery is not configured yet.',
        'recipient' => $mail['recipient'],
    ]);
}

if (!contact_rate_limit_allows(visitor_ip())) {
    header('Retry-After: 3600');
    contact_response(429, ['ok' => false, 'message' => 'Message limit reached. Please try again later.']);
}

try {
    $mailer = new PHPMailer(true);
    $mailer->isSMTP();
    $mailer->Host = $mail['host'];
    $mailer->Port = (int) $mail['port'];
    $mailer->SMTPAuth = true;
    $mailer->Username = $mail['username'];
    $mailer->Password = $mail['password'];
    $mailer->CharSet = PHPMailer::CHARSET_UTF8;

    $mailer->SMTPSecure = $mail['encryption'] === 'ssl'
        ? PHPMailer::ENCRYPTION_SMTPS
        : PHPMailer::ENCRYPTION_STARTTLS;
    $mailer->Timeout = 12;

    $mailer->setFrom($mail['from_email'], $mail['from_name']);
    $mailer->addAddress($mail['recipient']);
    $mailer->addReplyTo($email, $name);
    $mailer->Subject = 'Portfolio contact from ' . $name;
    $mailer->Body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";
    $mailer->send();

    contact_response(200, ['ok' => true, 'message' => 'Message sent. Thank you.']);
} catch (\Throwable $error) {
    error_log('Portfolio contact delivery failed: ' . $error->getMessage());
    contact_response(502, ['ok' => false, 'message' => 'Delivery failed. Please try again or use the contact email shown on the site.', 'recipient' => $mail['recipient']]);
}
