<?php

declare(strict_types=1);

/**
 * Bootstrap: loads config, core classes, models, and helpers.
 * Sessions are started here once for every page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_ROOT', dirname(__DIR__));

require_once APP_ROOT . '/app/config/app.php';
require_once APP_ROOT . '/app/config/database.php';

require_once APP_ROOT . '/app/core/Database.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Csrf.php';

require_once APP_ROOT . '/app/helpers/functions.php';

require_once APP_ROOT . '/app/models/User.php';
require_once APP_ROOT . '/app/models/Kategori.php';
require_once APP_ROOT . '/app/models/Produk.php';
require_once APP_ROOT . '/app/models/Transaksi.php';
require_once APP_ROOT . '/app/models/DetailTransaksi.php';

date_default_timezone_set(APP_TIMEZONE);
