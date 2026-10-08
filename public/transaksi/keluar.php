<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin', 'penjaga');

$transaksiModel = new Transaksi();
$produkModel = new Produk();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyRequest();

    $alasan = (string) ($_POST['alasan'] ?? 'keluar_terjual');
    $allowed = ['keluar_terjual', 'keluar_rusak', 'keluar_kedaluwarsa'];
    if (!in_array($alasan, $allowed, true)) {
        $alasan = 'keluar_terjual';
    }
    $keterangan = trim((string) ($_POST['keterangan'] ?? ''));
    $idProduks = $_POST['id_produk'] ?? [];
    $jumlahs = $_POST['jumlah'] ?? [];

    $items = [];
    $errors = [];

    foreach ((array) $idProduks as $i => $idProduk) {
        $idProduk = (int) $idProduk;
        $jumlah = (float) str_replace(',', '.', (string) ($jumlahs[$i] ?? '0'));

        if ($idProduk <= 0) {
            continue;
        }
        if ($jumlah <= 0) {
            $errors[] = 'Jumlah harus lebih dari 0 untuk setiap barang.';
            continue;
        }

        $p = $produkModel->find($idProduk);
        if ($p === null) {
            $errors[] = 'Barang tidak ditemukan.';
            continue;
        }
        if ((float) $p['stok'] < $jumlah) {
            $errors[] = 'Stok "' . $p['nama_produk'] . '" tidak cukup (sisa ' . $p['stok'] . ').';
            continue;
        }

        $items[] = [
            'id_produk' => $idProduk,
            'jumlah' => $jumlah,
            'harga_satuan' => (float) $p['harga_jual'],
        ];
    }

    if ($items === []) {
        $errors[] = 'Tambahkan minimal satu barang.';
    }

    if ($errors === []) {
        try {
            $transaksiModel->create($alasan, Auth::id(), $items, '', $keterangan);
            clear_old();
            flash('success', 'Barang keluar tercatat. Stok berkurang otomatis.');
            redirect('transaksi/keluar.php');
        } catch (Throwable $e) {
            $errors[] = 'Gagal menyimpan transaksi. Stok mungkin tidak mencukupi.';
        }
    }

    foreach ($errors as $err) {
        flash('error', $err);
    }
    set_old($_POST);
    redirect('transaksi/keluar.php');
}

$pageTitle = 'Barang Keluar';
$pageScript = 'transaksi.js';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <p class="panel-description">Catat barang keluar (terjual, rusak, atau kedaluwarsa). Stok berkurang otomatis; entri ditolak jika stok menjadi negatif.</p>

    <form method="post" action="<?= url('transaksi/keluar.php') ?>" class="form" data-transaksi-form data-mode="keluar">
        <?= Csrf::field() ?>

        <div class="form-row">
            <div class="form-group">
                <label for="alasan">Alasan</label>
                <select id="alasan" name="alasan">
                    <option value="keluar_terjual">Terjual</option>
                    <option value="keluar_rusak">Rusak</option>
                    <option value="keluar_kedaluwarsa">Kedaluwarsa</option>
                </select>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan (opsional)</label>
                <input type="text" id="keterangan" name="keterangan" value="<?= e(old('keterangan')) ?>" placeholder="Catatan">
            </div>
        </div>

        <h3 class="form-section-title">Daftar Barang</h3>
        <div data-item-list>
            <div class="item-row" data-item-row>
                <div class="form-group item-product">
                    <label>Barang</label>
                    <input type="text" class="item-search" placeholder="Ketik nama/kode..." autocomplete="off">
                    <input type="hidden" name="id_produk[]" class="item-id">
                    <div class="item-suggestions"></div>
                </div>
                <div class="form-group item-qty">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah[]" class="item-qty-input" min="0.01" step="any" required>
                </div>
                <div class="form-group item-stock">
                    <label>Sisa Stok</label>
                    <div class="item-stock-display">-</div>
                </div>
                <div class="form-group item-remove">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm" data-remove-row>&times;</button>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" data-add-row>+ Tambah Baris</button>
            <button type="submit" class="btn btn-primary">Simpan Barang Keluar</button>
        </div>
    </form>
</div>

<?php clear_old(); require APP_ROOT . '/app/views/layouts/footer.php'; ?>
