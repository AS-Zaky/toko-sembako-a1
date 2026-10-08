<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin', 'penjaga');

$produkModel = new Produk();
$transaksiModel = new Transaksi();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyRequest();

    $supplier = trim((string) ($_POST['supplier'] ?? ''));
    $keterangan = trim((string) ($_POST['keterangan'] ?? ''));
    $idProduks = $_POST['id_produk'] ?? [];
    $jumlahs = $_POST['jumlah'] ?? [];
    $hargas = $_POST['harga_satuan'] ?? [];

    $items = [];
    $errors = [];

    foreach ((array) $idProduks as $i => $idProduk) {
        $idProduk = (int) $idProduk;
        $jumlah = (float) str_replace(',', '.', (string) ($jumlahs[$i] ?? '0'));
        $harga = (float) str_replace(',', '.', (string) ($hargas[$i] ?? '0'));

        if ($idProduk <= 0) {
            continue;
        }
        if ($jumlah <= 0) {
            $errors[] = 'Jumlah harus lebih dari 0 untuk setiap barang.';
            continue;
        }
        if ($harga < 0) {
            $harga = 0;
        }
        $items[] = ['id_produk' => $idProduk, 'jumlah' => $jumlah, 'harga_satuan' => $harga];
    }

    if ($items === []) {
        $errors[] = 'Tambahkan minimal satu barang.';
    }

    if ($errors === []) {
        try {
            $transaksiModel->create('masuk', Auth::id(), $items, $supplier, $keterangan);
            clear_old();
            flash('success', 'Barang masuk tercatat. Stok bertambah otomatis.');
            redirect('transaksi/masuk.php');
        } catch (Throwable $e) {
            $errors[] = 'Gagal menyimpan transaksi. Silakan coba lagi.';
        }
    }

    foreach ($errors as $err) {
        flash('error', $err);
    }
    set_old($_POST);
    redirect('transaksi/masuk.php');
}

$pageTitle = 'Barang Masuk';
$pageScript = 'transaksi.js';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <p class="panel-description">Catat barang yang datang dari pemasok. Stok bertambah otomatis saat disimpan.</p>

    <form method="post" action="<?= url('transaksi/masuk.php') ?>" class="form" data-transaksi-form>
        <?= Csrf::field() ?>

        <div class="form-row">
            <div class="form-group">
                <label for="supplier">Pemasok / Supplier</label>
                <input type="text" id="supplier" name="supplier" value="<?= e(old('supplier')) ?>" placeholder="Nama pemasok">
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
                <div class="form-group item-price">
                    <label>Harga Beli / Satuan</label>
                    <input type="number" name="harga_satuan[]" class="item-price-input" min="0" step="any" required>
                </div>
                <div class="form-group item-remove">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm" data-remove-row>&times;</button>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" data-add-row>+ Tambah Baris</button>
            <button type="submit" class="btn btn-primary">Simpan Barang Masuk</button>
        </div>
    </form>
</div>

<?php clear_old(); require APP_ROOT . '/app/views/layouts/footer.php'; ?>
