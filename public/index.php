<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

if (Auth::check()) {
    redirect('dashboard.php');
}

redirect('login.php');
