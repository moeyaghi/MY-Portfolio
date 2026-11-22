<?php
declare(strict_types=1);

function site_config_value(string $key): string {
	static $local = null;
	if ($local === null) {
		$local = [];
		$path = __DIR__ . '/config.local.php';
		if (is_file($path)) {
			$local = require $path;
			if (!is_array($local)) { throw new RuntimeException('Local site configuration must return an array.'); }
		}
	}
	$value = getenv($key);
	if ($value === false) { $value = $local[$key] ?? ''; }
	return is_scalar($value) ? (string) $value : '';
}

// True only for a direct loopback request; tunnel/proxy requests (forwarded headers) are not local.
function is_local_request(): bool {
	if (PHP_SAPI === 'cli') {
		return true;
	}
	if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
		return false;
	}
	foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED_HOST', 'HTTP_FORWARDED'] as $h) {
		if (!empty($_SERVER[$h])) {
			return false;
		}
	}
	$host = strtolower(preg_replace('/:\d+$/', '', (string)($_SERVER['HTTP_HOST'] ?? '')));
	return in_array($host, ['localhost', '127.0.0.1', '[::1]', ''], true);
}

define('SITE_SESSION_NAME', 'myaghi_portfolio_session');
define('SITE_VERIFICATION_SECONDS', 3600);
define('DOWNLOAD_VERIFICATION_SECONDS', 300);
$configuredSiteUrl = rtrim(site_config_value('SITE_URL'), '/');
if ($configuredSiteUrl !== '') {
	$siteUrlParts = parse_url($configuredSiteUrl);
	if (filter_var($configuredSiteUrl, FILTER_VALIDATE_URL) === false || !is_array($siteUrlParts) || !in_array(strtolower((string) ($siteUrlParts['scheme'] ?? '')), ['http', 'https'], true) || empty($siteUrlParts['host']) || isset($siteUrlParts['user']) || isset($siteUrlParts['pass']) || isset($siteUrlParts['query']) || isset($siteUrlParts['fragment']) || !empty($siteUrlParts['path'])) {
		throw new RuntimeException('SITE_URL must be an absolute site origin without a path.');
	}
}
define('SITE_URL', $configuredSiteUrl);

