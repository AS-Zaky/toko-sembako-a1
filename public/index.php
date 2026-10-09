<?php

declare(strict_types=1);

// Public landing page. Logged-in users get a shortcut to the dashboard.
require_once dirname(__DIR__) . '/app/bootstrap.php';

$loggedIn = Auth::check();
$userName = $loggedIn ? (Auth::user()['nama'] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> - Sistem Pencatatan Stok</title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="landing-body">

<header class="landing-nav">
    <span class="landing-nav-brand"><?= e(APP_NAME) ?></span>
    <nav class="landing-nav-links">
        <a href="<?= url('katalog.php') ?>">Katalog</a>
        <?php if ($loggedIn): ?>
            <a href="<?= url('dashboard.php') ?>" class="btn btn-light btn-sm">Dashboard</a>
        <?php else: ?>
            <a href="<?= url('login.php') ?>" class="btn btn-light btn-sm">Masuk</a>
        <?php endif; ?>
    </nav>
</header>

<section class="landing-hero">
    <h1>Sistem Pencatatan Stok<br><?= e(APP_NAME) ?></h1>
    <p class="landing-tagline">
        Catat barang masuk dan keluar secara digital. Stok selalu akurat,
        harga selalu diingat, dan pemilik tahu kapan harus restock.
    </p>
    <div class="landing-actions">
        <a href="<?= url('katalog.php') ?>" class="btn btn-light btn-lg">Cek Ketersediaan Barang</a>
        <?php if ($loggedIn): ?>
            <a href="<?= url('dashboard.php') ?>" class="btn btn-outline-light btn-lg">Buka Dashboard</a>
        <?php else: ?>
            <a href="<?= url('login.php') ?>" class="btn btn-outline-light btn-lg">Masuk Karyawan</a>
        <?php endif; ?>
    </div>
    <?php if ($loggedIn): ?>
        <p class="landing-greeting">Anda sudah masuk sebagai <?= e($userName) ?>.</p>
    <?php endif; ?>
</section>

<section class="landing-features">
    <div class="landing-feature">
        <div class="landing-feature-icon">&#128230;</div>
        <h3>Stok Otomatis</h3>
        <p>Setiap barang masuk dan keluar langsung memperbarui jumlah stok. Tidak ada lagi hitungan manual yang keliru.</p>
    </div>
    <div class="landing-feature">
        <div class="landing-feature-icon">&#128178;</div>
        <h3>Cek Harga Cepat</h3>
        <p>Cari nama atau kode barang dan langsung lihat harga jual serta sisa stok — dalam hitungan detik.</p>
    </div>
    <div class="landing-feature">
        <div class="landing-feature-icon">&#9888;&#65039;</div>
        <h3>Peringatan Stok Menipis</h3>
        <p>Barang yang mencapai stok minimum otomatis ditandai, jadi pemilik tahu apa yang harus segera direstock.</p>
    </div>
    <div class="landing-feature">
        <div class="landing-feature-icon">&#128203;</div>
        <h3>Riwayat Tertelusur</h3>
        <p>Semua pergerakan stok tercatat rapi: tanggal, barang, jumlah, dan siapa yang mencatatnya.</p>
    </div>
</section>

<section class="landing-cta">
    <h2>Untuk pelanggan</h2>
    <p>Ingin tahu apakah barang tersedia sebelum datang ke toko? Cek katalog publik kami — tanpa perlu akun.</p>
    <a href="<?= url('katalog.php') ?>" class="btn btn-primary btn-lg">Buka Katalog</a>
</section>

<footer class="landing-footer">
    <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Sistem pencatatan stok sederhana untuk toko sembako.</p>
</footer>

</body>
</html>
