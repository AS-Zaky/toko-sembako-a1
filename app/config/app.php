<?php

declare(strict_types=1);

/**
 * Application settings.
 * Reads BASE_URL from .env when present so the app can live under a sub-path.
 */

define('APP_NAME', 'Toko Sembako A1');
define('APP_TIMEZONE', 'Asia/Jakarta');
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('APP_DEBUG', APP_ENV !== 'production');

$baseUrl = getenv('BASE_URL');
if ($baseUrl === false || $baseUrl === '') {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    // Pages inside sub-folders (produk/, transaksi/, ...) still resolve to the public root.
    $baseUrl = preg_replace('#/(produk|kategori|transaksi|riwayat|laporan|user)$#', '', $scriptDir);
    $baseUrl = rtrim((string) $baseUrl, '/');
}
define('BASE_URL', $baseUrl);

if (APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', APP_ROOT . '/storage/logs/php-error.log');
    error_reporting(E_ALL);
}
