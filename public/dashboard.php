<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
Auth::requireRole('admin', 'penjaga');

$produk = new Produk();
$transaksi = new Transaksi();

$totalProduk = $produk->countAll();
$totalMenipis = $produk->countLowStock();
$transaksiHariIni = $transaksi->countToday();
$lowStock = $produk->lowStock();
$recent = $transaksi->recent(10);

$pageTitle = 'Dashboard';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="cards">
    <div class="card">
        <div class="card-label">Jenis Produk</div>
        <div class="card-value"><?= (int) $totalProduk ?></div>
    </div>
    <div class="card card-warning">
        <div class="card-label">Stok Menipis / Habis</div>
        <div class="card-value"><?= (int) $totalMenipis ?></div>
    </div>
    <div class="card">
        <div class="card-label">Transaksi Hari Ini</div>
        <div class="card-value"><?= (int) $transaksiHariIni ?></div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2>Peringatan Stok Menipis</h2>
    </div>
    <?php if ($lowStock === []): ?>
        <p class="empty-state">Semua stok aman.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="text-right">Stok</th>
                    <th class="text-right">Min.</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($lowStock as $item): ?>
                    <tr>
                        <td><?= e($item['kode_produk']) ?></td>
                        <td><?= e($item['nama_produk']) ?></td>
                        <td><?= e($item['nama_kategori']) ?></td>
                        <td class="text-right"><?= e(rtrim(rtrim(number_format((float) $item['stok'], 2, ',', '.'), '0'), ',')) ?> <?= e($item['satuan']) ?></td>
                        <td class="text-right"><?= e(rtrim(rtrim(number_format((float) $item['stok_minimum'], 2, ',', '.'), '0'), ',')) ?></td>
                        <td><span class="badge <?= (float) $item['stok'] <= 0 ? 'badge-danger' : 'badge-warning' ?>">
                            <?= (float) $item['stok'] <= 0 ? 'Habis' : 'Menipis' ?>
                        </span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="panel">
    <div class="panel-header">
        <h2>Aktivitas Terakhir</h2>
    </div>
    <?php if ($recent === []): ?>
        <p class="empty-state">Belum ada transaksi.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Oleh</th>
                    <th class="text-right">Total</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($recent as $row): ?>
                    <tr>
                        <td><?= e(format_tanggal($row['tanggal_transaksi'])) ?></td>
                        <td><?= e(label_jenis_transaksi($row['jenis_transaksi'])) ?></td>
                        <td><?= e($row['nama_user']) ?></td>
                        <td class="text-right"><?= e(format_rupiah($row['total_harga'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
