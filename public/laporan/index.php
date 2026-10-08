<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

$transaksiModel = new Transaksi();

$dari = (string) ($_GET['dari'] ?? date('Y-m-01'));
$sampai = (string) ($_GET['sampai'] ?? date('Y-m-d'));
$rows = $transaksiModel->report($dari, $sampai);

$pageTitle = 'Laporan Inventaris';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <form method="get" action="<?= url('laporan/index.php') ?>" class="filter-bar">
        <label>Dari <input type="date" name="dari" value="<?= e($dari) ?>"></label>
        <label>Sampai <input type="date" name="sampai" value="<?= e($sampai) ?>"></label>
        <button type="submit" class="btn btn-secondary">Terapkan</button>
    </form>

    <p class="panel-description">Periode: <?= e(format_tanggal_pendek($dari)) ?> s.d. <?= e(format_tanggal_pendek($sampai)) ?></p>

    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th class="text-right">Masuk</th>
                <th class="text-right">Terjual</th>
                <th class="text-right">Rusak</th>
                <th class="text-right">Kedaluwarsa</th>
                <th class="text-right">Penyesuaian</th>
                <th class="text-right">Stok Saat Ini</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row):
                $fmt = static fn ($v): string => rtrim(rtrim(number_format((float) $v, 2, ',', '.'), '0'), ',');
                ?>
                <tr>
                    <td><?= e($row['kode_produk']) ?></td>
                    <td><?= e($row['nama_produk']) ?> (<?= e($row['satuan']) ?>)</td>
                    <td class="text-right"><?= e($fmt($row['total_masuk'])) ?></td>
                    <td class="text-right"><?= e($fmt($row['total_terjual'])) ?></td>
                    <td class="text-right"><?= e($fmt($row['total_rusak'])) ?></td>
                    <td class="text-right"><?= e($fmt($row['total_kedaluwarsa'])) ?></td>
                    <td class="text-right"><?= e($fmt($row['total_penyesuaian'])) ?></td>
                    <td class="text-right"><strong><?= e($fmt($row['stok'])) ?></strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
