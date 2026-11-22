<?php
declare(strict_types=1);

require_once __DIR__ . '/site_settings.php';

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function recaptcha_state_dir(): string {
    $dir = dirname(__DIR__) . '/var/recaptcha';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return $dir;
}

function recaptcha_log_file(): string {
    return recaptcha_state_dir() . '/failures.log';
}

function recaptcha_stats_file(): string {
    return recaptcha_state_dir() . '/stats.json';
}

function recaptcha_ip_state_file(string $action, string $ip): string {
    $id = sha1(strtolower($action) . '|' . strtolower($ip));
    return recaptcha_state_dir() . '/ip_' . $id . '.json';
}

function recaptcha_read_json_file(string $path): array {
    if (!is_file($path)) {
        return [];
    }
    $content = @file_get_contents($path);
    if ($content === false || $content === '') {
        return [];
    }
    $json = json_decode($content, true);
    return is_array($json) ? $json : [];
}

function recaptcha_write_json_file(string $path, array $data): void {
    @file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function recaptcha_allowed_hosts(): array {
    return site_recaptcha_settings()['allowed_hosts'];
}

function recaptcha_setting_integer(string $key): int {
    $value = site_setting_integer('recaptcha.' . $key);
    if ($value === null || $value < 1) { throw new RuntimeException('reCAPTCHA settings are incomplete.'); }
    return $value;
}

function recaptcha_score_bucket(?float $score): string {
    if ($score === null) {
        return 'none';
    }
    if ($score < 0.1) return '0.0-0.1';
    if ($score < 0.2) return '0.1-0.2';
    if ($score < 0.3) return '0.2-0.3';
    if ($score < 0.4) return '0.3-0.4';
    if ($score < 0.5) return '0.4-0.5';
    if ($score < 0.6) return '0.5-0.6';
    if ($score < 0.7) return '0.6-0.7';
    if ($score < 0.8) return '0.7-0.8';
    if ($score < 0.9) return '0.8-0.9';
    return '0.9-1.0';
}

function recaptcha_log_failure(
    string $action,
    string $message,
    ?float $score,
    array $reasonCodes = [],
    array $extra = []
): void {
    $record = [
        'ts' => gmdate('c'),
        'ip' => visitor_ip(),
        'host' => request_host(),
        'action' => $action,
        'message' => $message,
        'score' => $score,
        'score_bucket' => recaptcha_score_bucket($score),
        'reason_codes' => array_values($reasonCodes),
        'extra' => $extra,
    ];

    @file_put_contents(recaptcha_log_file(), json_encode($record, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);

    $stats = recaptcha_read_json_file(recaptcha_stats_file());
    if (!isset($stats['failure_reasons']) || !is_array($stats['failure_reasons'])) {
        $stats['failure_reasons'] = [];
    }
    if (!isset($stats['score_buckets']) || !is_array($stats['score_buckets'])) {
        $stats['score_buckets'] = [];
    }

    foreach ($reasonCodes as $code) {
        $key = (string)$code;
        if ($key === '') {
            continue;
        }
        if (!isset($stats['failure_reasons'][$key])) {
            $stats['failure_reasons'][$key] = 0;
        }
        $stats['failure_reasons'][$key]++;
    }

    $bucket = recaptcha_score_bucket($score);
    if (!isset($stats['score_buckets'][$bucket])) {
        $stats['score_buckets'][$bucket] = 0;
    }
    $stats['score_buckets'][$bucket]++;
    $stats['updated_at'] = gmdate('c');

    recaptcha_write_json_file(recaptcha_stats_file(), $stats);
}

function visitor_ip(): string {
    return $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
}

function request_host(): string {
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    $parts = explode(':', $host, 2);
    return trim($parts[0]);
}

function recaptcha_normalize_state(array $state): array {
    return [
        'attempts' => isset($state['attempts']) && is_array($state['attempts']) ? $state['attempts'] : [],
        'failures' => isset($state['failures']) && is_array($state['failures']) ? $state['failures'] : [],
        'lock_until' => isset($state['lock_until']) ? (int)$state['lock_until'] : 0,
    ];
}

function recaptcha_prune_timestamps(array $values, int $window, int $now): array {
    $minTs = $now - $window;
    $pruned = [];
    foreach ($values as $ts) {
        $tsInt = (int)$ts;
        if ($tsInt >= $minTs) {
            $pruned[] = $tsInt;
        }
    }
    return $pruned;
}

function recaptcha_get_ip_state(string $action, string $ip): array {
    return recaptcha_normalize_state(recaptcha_read_json_file(recaptcha_ip_state_file($action, $ip)));
}

function recaptcha_set_ip_state(string $action, string $ip, array $state): void {
    recaptcha_write_json_file(recaptcha_ip_state_file($action, $ip), recaptcha_normalize_state($state));
}

function recaptcha_get_session_state(string $action): array {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return recaptcha_normalize_state([]);
    }
    $all = isset($_SESSION['recaptcha_state']) && is_array($_SESSION['recaptcha_state'])
        ? $_SESSION['recaptcha_state']
        : [];
    $state = isset($all[$action]) && is_array($all[$action]) ? $all[$action] : [];
    return recaptcha_normalize_state($state);
}

function recaptcha_set_session_state(string $action, array $state): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    if (!isset($_SESSION['recaptcha_state']) || !is_array($_SESSION['recaptcha_state'])) {
        $_SESSION['recaptcha_state'] = [];
    }
    $_SESSION['recaptcha_state'][$action] = recaptcha_normalize_state($state);
}

function recaptcha_load_states(string $action, string $ip): array {
    return [
        'ip' => recaptcha_get_ip_state($action, $ip),
        'session' => recaptcha_get_session_state($action),
    ];
}

function recaptcha_save_states(string $action, string $ip, array $states): void {
    recaptcha_set_ip_state($action, $ip, $states['ip']);
    recaptcha_set_session_state($action, $states['session']);
}

function recaptcha_check_locked(array $states, int $now): bool {
    return ((int)$states['ip']['lock_until'] > $now) || ((int)$states['session']['lock_until'] > $now);
}

function recaptcha_locked_seconds(array $states, int $now): int {
    $until = max((int)$states['ip']['lock_until'], (int)$states['session']['lock_until']);
    return max(0, $until - $now);
}

function recaptcha_register_attempts(array &$states, int $now): void {
    $window = recaptcha_setting_integer('rate_window_seconds');

    $states['ip']['attempts'] = recaptcha_prune_timestamps($states['ip']['attempts'], $window, $now);
    $states['session']['attempts'] = recaptcha_prune_timestamps($states['session']['attempts'], $window, $now);

    $states['ip']['attempts'][] = $now;
    $states['session']['attempts'][] = $now;
}

function recaptcha_attempt_limit_hit(array $states): bool {
    $maxIp = recaptcha_setting_integer('max_attempts_ip');
    $maxSession = recaptcha_setting_integer('max_attempts_session');

    return count($states['ip']['attempts']) > $maxIp || count($states['session']['attempts']) > $maxSession;
}

function recaptcha_register_failure_state(array &$states, int $now): bool {
    $window = recaptcha_setting_integer('failure_window_seconds');
    $threshold = recaptcha_setting_integer('lockout_failures');
    $lockSeconds = recaptcha_setting_integer('lockout_seconds');

    $states['ip']['failures'] = recaptcha_prune_timestamps($states['ip']['failures'], $window, $now);
    $states['session']['failures'] = recaptcha_prune_timestamps($states['session']['failures'], $window, $now);

    $states['ip']['failures'][] = $now;
    $states['session']['failures'][] = $now;

    $shouldLock = count($states['ip']['failures']) >= $threshold || count($states['session']['failures']) >= $threshold;
    if ($shouldLock) {
        $lockUntil = $now + $lockSeconds;
        $states['ip']['lock_until'] = $lockUntil;
        $states['session']['lock_until'] = $lockUntil;
    }
    return $shouldLock;
}

function recaptcha_clear_failure_state(array &$states): void {
    $states['ip']['failures'] = [];
    $states['session']['failures'] = [];
    $states['ip']['lock_until'] = 0;
    $states['session']['lock_until'] = 0;
}

function is_localhost_request(): bool {
    return PHP_SAPI !== 'cli' && is_local_request();
}

function recaptcha_bypass_enabled(): bool {
    return site_setting_enabled('recaptcha.disable_on_localhost') && is_localhost_request();
}

function recaptcha_ready(): bool {
    return site_recaptcha_ready();
}

function recaptcha_http_post_json(string $url, string $payload): array {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        if ($ch !== false) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 12);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            $response = curl_exec($ch);
            if ($response !== false) {
                $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                return [
                    'ok' => $status >= 200 && $status < 500,
                    'response' => (string)$response,
                    'error' => '',
                ];
            }

            $error = curl_error($ch);
            curl_close($ch);
            return [
                'ok' => false,
                'response' => '',
                'error' => $error !== '' ? $error : 'cURL request failed',
            ];
        }
    }

    if ((bool)ini_get('allow_url_fopen')) {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 12,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response !== false) {
            return [
                'ok' => true,
                'response' => (string)$response,
                'error' => '',
            ];
        }
    }

    return [
        'ok' => false,
        'response' => '',
        'error' => 'No HTTP transport available (cURL unavailable and allow_url_fopen disabled).',
    ];
}

function verify_recaptcha_enterprise(string $token, string $expectedAction): array {
    $settings = site_recaptcha_settings();
    $minimumScore = match ($expectedAction) {
        'CONTACT_MESSAGE' => $settings['minimum_score_site'],
        'DOWNLOAD_CV' => $settings['minimum_score_download'],
        default => null,
    };
    if ($minimumScore === null || !site_recaptcha_policy_ready()) {
        recaptcha_log_failure($expectedAction, 'reCAPTCHA action is not configured.', null, ['not_configured']);
        return ['success' => false, 'message' => 'reCAPTCHA is not configured correctly.', 'score' => null];
    }

    $now = time();
    $ip = visitor_ip();
    $states = recaptcha_load_states($expectedAction, $ip);

    if (recaptcha_check_locked($states, $now)) {
        $remaining = recaptcha_locked_seconds($states, $now);
        recaptcha_log_failure($expectedAction, 'Temporarily locked after repeated failures.', null, ['temporary_lockout'], ['remaining_seconds' => $remaining]);
        return [
            'success' => false,
            'message' => 'Too many failed attempts. Please wait before trying again.',
            'score' => null,
        ];
    }

    recaptcha_register_attempts($states, $now);
    if (recaptcha_attempt_limit_hit($states)) {
        $lockSeconds = recaptcha_setting_integer('lockout_seconds');
        $states['ip']['lock_until'] = $now + $lockSeconds;
        $states['session']['lock_until'] = $now + $lockSeconds;
        recaptcha_save_states($expectedAction, $ip, $states);

        recaptcha_log_failure($expectedAction, 'Rate limit exceeded.', null, ['rate_limit']);
        return [
            'success' => false,
            'message' => 'Too many attempts. Please wait and try again.',
            'score' => null,
        ];
    }
    recaptcha_save_states($expectedAction, $ip, $states);

    if (!recaptcha_ready()) {
        recaptcha_register_failure_state($states, $now);
        recaptcha_save_states($expectedAction, $ip, $states);
        recaptcha_log_failure($expectedAction, 'reCAPTCHA is not configured.', null, ['not_configured']);
        return ['success' => false, 'message' => 'reCAPTCHA is not configured correctly.', 'score' => null];
    }

    if ($token === '') {
        recaptcha_register_failure_state($states, $now);
        recaptcha_save_states($expectedAction, $ip, $states);
        recaptcha_log_failure($expectedAction, 'Missing reCAPTCHA token.', null, ['missing_token']);
        return [
            'success' => false,
            'message' => 'Missing reCAPTCHA token.',
            'score' => null,
        ];
    }

    $url = 'https://recaptchaenterprise.googleapis.com/v1/projects/'
        . rawurlencode($settings['project_id'])
        . '/assessments?key='
        . rawurlencode($settings['api_key']);

    $payload = json_encode([
        'event' => [
            'token' => $token,
            'siteKey' => $settings['site_key'],
            'expectedAction' => $expectedAction,
            'userIpAddress' => visitor_ip(),
            'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ],
    ], JSON_UNESCAPED_SLASHES);

    $transport = recaptcha_http_post_json($url, $payload);

    if (!$transport['ok']) {
        recaptcha_register_failure_state($states, $now);
        recaptcha_save_states($expectedAction, $ip, $states);
        recaptcha_log_failure(
            $expectedAction,
            'Could not contact reCAPTCHA service.',
            null,
            ['service_unreachable'],
            ['transport_error' => (string)$transport['error']]
        );
        return [
            'success' => false,
            'message' => 'Could not contact reCAPTCHA verification service.',
            'score' => null,
        ];
    }

    $response = (string)$transport['response'];

    $json = json_decode($response, true);

    if (!is_array($json)) {
        recaptcha_register_failure_state($states, $now);
        recaptcha_save_states($expectedAction, $ip, $states);
        recaptcha_log_failure($expectedAction, 'Invalid API response.', null, ['invalid_api_response']);
        return [
            'success' => false,
            'message' => 'Invalid reCAPTCHA response.',
            'score' => null,
        ];
    }

    if (isset($json['error'])) {
        $apiMessage = (string)($json['error']['message'] ?? 'Unknown error');
        recaptcha_register_failure_state($states, $now);
        recaptcha_save_states($expectedAction, $ip, $states);
        recaptcha_log_failure($expectedAction, 'reCAPTCHA API error.', null, ['api_error'], ['api_message' => $apiMessage]);
        return [
            'success' => false,
            'message' => 'reCAPTCHA API error: ' . $apiMessage,
            'score' => null,
        ];
    }

    $valid = (bool)($json['tokenProperties']['valid'] ?? false);
    $action = (string)($json['tokenProperties']['action'] ?? '');
    $hostname = strtolower((string)($json['tokenProperties']['hostname'] ?? ''));
    $invalidReason = (string)($json['tokenProperties']['invalidReason'] ?? '');
    $riskReasons = isset($json['riskAnalysis']['reasons']) && is_array($json['riskAnalysis']['reasons'])
        ? $json['riskAnalysis']['reasons']
        : [];
    $score = isset($json['riskAnalysis']['score']) ? (float)$json['riskAnalysis']['score'] : 0.0;
    $allowedHosts = recaptcha_allowed_hosts();

    $reasonCodes = [];
    if ($invalidReason !== '') {
        $reasonCodes[] = 'invalid_reason:' . $invalidReason;
    }
    foreach ($riskReasons as $riskReason) {
        $riskCode = trim((string)$riskReason);
        if ($riskCode !== '') {
            $reasonCodes[] = 'risk_reason:' . $riskCode;
        }
    }

    if (!$valid) {
        recaptcha_register_failure_state($states, $now);
        recaptcha_save_states($expectedAction, $ip, $states);
        $reasonCodes[] = 'token_invalid';
        recaptcha_log_failure($expectedAction, 'Invalid or expired token.', $score, $reasonCodes, ['hostname' => $hostname]);
        return [
            'success' => false,
            'message' => 'Invalid or expired reCAPTCHA token.',
            'score' => $score,
        ];
    }

    if ($action !== $expectedAction) {
        recaptcha_register_failure_state($states, $now);
        recaptcha_save_states($expectedAction, $ip, $states);
        $reasonCodes[] = 'action_mismatch';
        recaptcha_log_failure($expectedAction, 'Action mismatch.', $score, $reasonCodes, ['response_action' => $action]);
        return [
            'success' => false,
            'message' => 'Invalid reCAPTCHA action.',
            'score' => $score,
        ];
    }

    if ($hostname === '' || !in_array($hostname, $allowedHosts, true)) {
        recaptcha_register_failure_state($states, $now);
        recaptcha_save_states($expectedAction, $ip, $states);
        $reasonCodes[] = 'hostname_mismatch';
        recaptcha_log_failure($expectedAction, 'Hostname validation failed.', $score, $reasonCodes, ['response_hostname' => $hostname, 'allowed_hosts' => $allowedHosts]);
        return [
            'success' => false,
            'message' => 'Invalid reCAPTCHA hostname.',
            'score' => $score,
        ];
    }

    if ($score < $minimumScore) {
        $locked = recaptcha_register_failure_state($states, $now);
        recaptcha_save_states($expectedAction, $ip, $states);
        $reasonCodes[] = 'score_too_low';
        recaptcha_log_failure($expectedAction, 'Score below threshold.', $score, $reasonCodes, ['minimum_score' => $minimumScore, 'locked' => $locked]);
        return [
            'success' => false,
            'message' => 'Verification score was too low. Please try again.',
            'score' => $score,
        ];
    }

    recaptcha_clear_failure_state($states);
    recaptcha_save_states($expectedAction, $ip, $states);

    return [
        'success' => true,
        'message' => 'Verified.',
        'score' => $score,
    ];
}
