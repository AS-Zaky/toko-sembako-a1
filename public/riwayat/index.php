<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin', 'penjaga');

$transaksiModel = new Transaksi();
$jenis = (string) ($_GET['jenis'] ?? '');
$dari = (string) ($_GET['dari'] ?? '');
$sampai = (string) ($_GET['sampai'] ?? '');

$rows = $transaksiModel->history($jenis, $dari, $sampai);

$jenisOptions = [
    '' => 'Semua Jenis',
    'masuk' => 'Barang Masuk',
    'keluar_terjual' => 'Terjual',
    'keluar_rusak' => 'Rusak',
    'keluar_kedaluwarsa' => 'Kedaluwarsa',
    'penyesuaian' => 'Penyesuaian Stok',
];

$pageTitle = 'Riwayat Stok';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <form method="get" action="<?= url('riwayat/index.php') ?>" class="filter-bar">
        <select name="jenis">
            <?php foreach ($jenisOptions as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $jenis === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="dari" value="<?= e($dari) ?>" aria-label="Dari tanggal">
        <input type="date" name="sampai" value="<?= e($sampai) ?>" aria-label="Sampai tanggal">
        <button type="submit" class="btn btn-secondary">Terapkan</button>
        <a href="<?= url('riwayat/index.php') ?>" class="btn btn-light">Atur Ulang</a>
    </form>

    <?php if ($rows === []): ?>
        <p class="empty-state">Belum ada riwayat transaksi.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Oleh</th>
                    <th>Pemasok</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Detail</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e(format_tanggal($row['tanggal_transaksi'])) ?></td>
                        <td><?= e(label_jenis_transaksi($row['jenis_transaksi'])) ?></td>
                        <td><?= e($row['nama_user']) ?></td>
                        <td><?= e($row['supplier'] ?? '-') ?></td>
                        <td class="text-right"><?= e(format_rupiah($row['total_harga'])) ?></td>
                        <td class="text-center">
                            <a href="<?= url('transaksi/detail.php?id=' . (int) $row['id_transaksi']) ?>" class="btn btn-sm btn-secondary">Lihat</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
