<?php

$envFile = __DIR__ . '/../.env';

if (!file_exists($envFile)) {
    die("HATA: .env dosyası bulunamadı!");
}

$env = parse_ini_file($envFile);

if ($env === false) {
    die("HATA: .env dosyası okunamadı!");
}

$appDebug = in_array(strtolower($env['APP_DEBUG'] ?? 'false'), ['true', '1', 'on', 'yes']);

if ($appDebug) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

if (!defined('SITE_URL')) {
    define('SITE_URL', rtrim($env['SITE_URL'] ?? 'http://localhost', '/') . '/');
}

if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', rtrim(SITE_URL, '/') . '/public/assets/');
}

return [
    'db_host' => $env['DB_HOST'] ?? 'localhost',
    'db_name' => $env['DB_NAME'] ?? '',
    'db_user' => $env['DB_USER'] ?? '',
    'db_pass' => $env['DB_PASS'] ?? '',
    'app_env' => $env['APP_ENV'] ?? 'production',
    'app_debug' => $appDebug,
];