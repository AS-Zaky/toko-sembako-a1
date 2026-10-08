<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin', 'penjaga');

$id = (int) ($_GET['id'] ?? 0);
$transaksiModel = new Transaksi();
$detailModel = new DetailTransaksi();

$trx = $transaksiModel->find($id);
if ($trx === null) {
    flash('error', 'Transaksi tidak ditemukan.');
    redirect('riwayat/index.php');
}
$details = $detailModel->forTransaksi($id);

$pageTitle = 'Detail Transaksi #' . $id;
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <div class="detail-meta">
        <div><strong>Tanggal:</strong> <?= e(format_tanggal($trx['tanggal_transaksi'])) ?></div>
        <div><strong>Jenis:</strong> <?= e(label_jenis_transaksi($trx['jenis_transaksi'])) ?></div>
        <div><strong>Oleh:</strong> <?= e($trx['nama_user']) ?></div>
        <?php if (!empty($trx['supplier'])): ?>
            <div><strong>Pemasok:</strong> <?= e($trx['supplier']) ?></div>
        <?php endif; ?>
        <?php if (!empty($trx['keterangan'])): ?>
            <div><strong>Keterangan:</strong> <?= e($trx['keterangan']) ?></div>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($details as $d): ?>
                <tr>
                    <td><?= e($d['kode_produk']) ?></td>
                    <td><?= e($d['nama_produk']) ?></td>
                    <td class="text-right"><?= e(rtrim(rtrim(number_format((float) $d['jumlah'], 2, ',', '.'), '0'), ',')) ?> <?= e($d['satuan']) ?></td>
                    <td class="text-right"><?= e(format_rupiah($d['harga_satuan'])) ?></td>
                    <td class="text-right"><?= e(format_rupiah($d['subtotal'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total</th>
                <th class="text-right"><?= e(format_rupiah($trx['total_harga'])) ?></th>
            </tr>
            </tfoot>
        </table>
    </div>

    <a href="<?= url('riwayat/index.php') ?>" class="btn btn-secondary">Kembali ke Riwayat</a>
</div>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
