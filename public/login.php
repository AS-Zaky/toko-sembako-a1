<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

if (Auth::check()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyRequest();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (Auth::attempt($username, $password)) {
        flash('success', 'Selamat datang, ' . (Auth::user()['nama'] ?? '') . '.');
        redirect('dashboard.php');
    }

    flash('error', 'Username atau kata sandi salah.');
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="auth-body">
<div class="auth-card">
    <h1 class="auth-title"><?= e(APP_NAME) ?></h1>
    <p class="auth-subtitle">Sistem Pencatatan Stok</p>

    <?php require APP_ROOT . '/app/views/partials/alert.php'; ?>

    <form method="post" action="<?= url('login.php') ?>" class="form">
        <?= Csrf::field() ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus autocomplete="username">
        </div>
        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
    </form>

    <p class="auth-link"><a href="<?= url('katalog.php') ?>">Lihat ketersediaan barang (katalog publik)</a></p>
</div>
</body>
</html>
