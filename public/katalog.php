<?php

declare(strict_types=1);

// Public catalog - no login required. Status only; purchase price is never shown.
require_once dirname(__DIR__) . '/app/bootstrap.php';

$produk = new Produk();
$search = trim((string) ($_GET['q'] ?? ''));
$items = $produk->catalog($search);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="catalog-body">
<div class="catalog-container">
    <header class="catalog-header">
        <h1><?= e(APP_NAME) ?></h1>
        <p>Cek ketersediaan barang sebelum datang ke toko.</p>
        <form method="get" action="<?= url('katalog.php') ?>" class="catalog-search">
            <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari nama atau kode barang...">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </header>

    <?php if ($items === []): ?>
        <p class="empty-state">Barang tidak ditemukan.</p>
    <?php else: ?>
        <div class="catalog-grid">
            <?php foreach ($items as $item):
                $status = status_stok($item['stok'], $item['stok_minimum']);
                $statusClass = match ($status) {
                    'Tersedia' => 'status-tersedia',
                    'Menipis' => 'status-menipis',
                    default => 'status-habis',
                };
                ?>
                <div class="catalog-card">
                    <div class="catalog-card-name"><?= e($item['nama_produk']) ?></div>
                    <div class="catalog-card-meta"><?= e($item['nama_kategori']) ?> &middot; <?= e($item['satuan']) ?></div>
                    <div class="catalog-card-price"><?= e(format_rupiah($item['harga_jual'])) ?></div>
                    <div class="catalog-card-status <?= $statusClass ?>"><?= e($status) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <footer class="catalog-footer">
        <a href="<?= url('login.php') ?>">Masuk (karyawan)</a>
    </footer>
</div>
</body>
</html>
