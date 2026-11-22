<?php
declare(strict_types=1);

function site_settings_all(): array
{
    static $settings = null;
    if ($settings !== null) { return $settings; }

    $variables = [
        'site.maintenance_mode' => 'MAINTENANCE_MODE',
        'site.copyright_text' => 'COPYRIGHT_TEXT',
        'captcha.site_enabled' => 'CAPTCHA_SITE_ENABLED',
        'captcha.download_enabled' => 'CAPTCHA_DOWNLOAD_ENABLED',
        'mail.enabled' => 'CONTACT_MAIL_ENABLED',
        'mail.recipient' => 'CONTACT_EMAIL_TO',
        'mail.smtp_host' => 'CONTACT_SMTP_HOST',
        'mail.smtp_port' => 'CONTACT_SMTP_PORT',
        'mail.smtp_encryption' => 'CONTACT_SMTP_ENCRYPTION',
        'mail.smtp_username' => 'CONTACT_SMTP_USERNAME',
        'mail.smtp_password' => 'CONTACT_SMTP_PASSWORD',
        'mail.from_email' => 'CONTACT_SMTP_FROM',
        'mail.from_name' => 'CONTACT_SMTP_FROM_NAME',
        'recaptcha.site_key' => 'RECAPTCHA_SITE_KEY',
        'recaptcha.project_id' => 'RECAPTCHA_PROJECT_ID',
        'recaptcha.api_key' => 'RECAPTCHA_API_KEY',
        'recaptcha.allowed_hosts' => 'RECAPTCHA_ALLOWED_HOSTS',
        'recaptcha.minimum_score_site' => 'RECAPTCHA_MIN_SCORE_SITE',
        'recaptcha.minimum_score_download' => 'RECAPTCHA_MIN_SCORE_DOWNLOAD',
        'recaptcha.disable_on_localhost' => 'RECAPTCHA_DISABLE_ON_LOCALHOST',
        'recaptcha.rate_window_seconds' => 'RECAPTCHA_RATE_WINDOW_SECONDS',
        'recaptcha.failure_window_seconds' => 'RECAPTCHA_FAILURE_WINDOW_SECONDS',
        'recaptcha.max_attempts_ip' => 'RECAPTCHA_MAX_ATTEMPTS_IP',
        'recaptcha.max_attempts_session' => 'RECAPTCHA_MAX_ATTEMPTS_SESSION',
        'recaptcha.lockout_failures' => 'RECAPTCHA_LOCKOUT_FAILURES',
        'recaptcha.lockout_seconds' => 'RECAPTCHA_LOCKOUT_SECONDS',
    ];

    $settings = ['recaptcha.enterprise' => '1'];
    foreach ($variables as $key => $variable) {
        $settings[$key] = site_config_value($variable);
    }
    return $settings;
}

function site_setting(string $key): string
{
    return site_settings_all()[$key] ?? '';
}

function site_setting_enabled(string $key): bool
{
    if (in_array($key, ['captcha.site_enabled', 'captcha.download_enabled'], true)) {
        return site_setting($key) !== '0';
    }
    return site_setting($key) === '1';
}

function site_setting_integer(string $key): ?int
{
    $value = site_setting($key);
    if ($value === '' || filter_var($value, FILTER_VALIDATE_INT) === false) { return null; }
    return (int) $value;
}

function site_setting_score(string $key): ?float
{
    $value = site_setting($key);
    if ($value === '' || !is_numeric($value)) { return null; }
    $score = (float) $value;
    return is_finite($score) && $score >= 0 && $score <= 1 ? $score : null;
}

function site_current_origin(): string
{
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    if (preg_match('/\A(?:[a-z0-9](?:[a-z0-9.-]{0,251}[a-z0-9])?|\[[0-9a-f:.]+\])(?::[0-9]{1,5})?\z/iD', $host) !== 1 || filter_var('http://' . $host, FILTER_VALIDATE_URL) === false) {
        return rtrim(SITE_URL, '/');
    }
    $requestHost = parse_url('http://' . $host, PHP_URL_HOST);
    $configuredHost = parse_url(SITE_URL, PHP_URL_HOST);
    $configuredScheme = parse_url(SITE_URL, PHP_URL_SCHEME);
    $https = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443
        || (is_string($requestHost) && is_string($configuredHost) && strcasecmp($requestHost, $configuredHost) === 0 && $configuredScheme === 'https');
    return ($https ? 'https://' : 'http://') . $host;
}

function site_mail_settings(): array
{
    $password = site_setting('mail.smtp_password');
    return [
        'enabled' => site_setting_enabled('mail.enabled'),
        'recipient' => site_setting('mail.recipient'),
        'host' => site_setting('mail.smtp_host'),
        'port' => site_setting('mail.smtp_port'),
        'encryption' => site_setting('mail.smtp_encryption'),
        'username' => site_setting('mail.smtp_username'),
        'password' => $password,
        'password_configured' => $password !== '',
        'from_email' => site_setting('mail.from_email'),
        'from_name' => site_setting('mail.from_name'),
    ];
}

function site_recaptcha_settings(): array
{
    $apiKey = site_setting('recaptcha.api_key');
    $hosts = array_values(array_unique(array_filter(array_map(
        static fn(string $host): string => strtolower(trim($host)),
        explode(',', site_setting('recaptcha.allowed_hosts'))
    ), static fn(string $host): bool => $host !== '')));
    $limits = [];
    foreach (['rate_window_seconds', 'failure_window_seconds', 'max_attempts_ip', 'max_attempts_session', 'lockout_failures', 'lockout_seconds'] as $name) {
        $limits[$name] = site_setting_integer('recaptcha.' . $name);
    }
    return [
        'site_key' => site_setting('recaptcha.site_key'),
        'project_id' => site_setting('recaptcha.project_id'),
        'api_key' => $apiKey,
        'api_key_configured' => $apiKey !== '',
        'allowed_hosts' => $hosts,
        'allowed_hosts_text' => site_setting('recaptcha.allowed_hosts'),
        'minimum_score_site' => site_setting_score('recaptcha.minimum_score_site'),
        'minimum_score_download' => site_setting_score('recaptcha.minimum_score_download'),
        'disable_on_localhost' => site_setting_enabled('recaptcha.disable_on_localhost'),
        'enterprise' => true,
        'limits' => $limits,
    ];
}

function site_recaptcha_policy_ready(): bool
{
    $settings = site_recaptcha_settings();
    if ($settings['minimum_score_site'] === null || $settings['minimum_score_download'] === null) { return false; }
    foreach ($settings['limits'] as $limit) {
        if ($limit === null || $limit < 1) { return false; }
    }
    return true;
}

function site_recaptcha_ready(): bool
{
    $settings = site_recaptcha_settings();
    return $settings['site_key'] !== '' && $settings['project_id'] !== '' && $settings['api_key_configured'] && $settings['allowed_hosts'] && site_recaptcha_policy_ready();
}