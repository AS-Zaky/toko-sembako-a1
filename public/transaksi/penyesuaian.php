<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

$transaksiModel = new Transaksi();
$produkModel = new Produk();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyRequest();

    $idProduk = (int) ($_POST['id_produk'] ?? 0);
    $stokFisik = (float) str_replace(',', '.', (string) ($_POST['stok_fisik'] ?? '0'));
    $keterangan = trim((string) ($_POST['keterangan'] ?? ''));

    $errors = [];

    $p = $idProduk > 0 ? $produkModel->find($idProduk) : null;
    if ($p === null) {
        $errors[] = 'Pilih barang yang akan disesuaikan.';
    }
    if ($stokFisik < 0) {
        $errors[] = 'Stok fisik tidak boleh negatif.';
    }
    if ($keterangan === '') {
        $errors[] = 'Catatan wajib diisi untuk penyesuaian stok.';
    }

    if ($errors === [] && $p !== null) {
        $selisih = $stokFisik - (float) $p['stok'];
        if ($selisih == 0.0) {
            flash('success', 'Stok sudah sesuai, tidak ada perubahan.');
            redirect('transaksi/penyesuaian.php');
        }

        try {
            $transaksiModel->create('penyesuaian', Auth::id(), [
                ['id_produk' => $idProduk, 'jumlah' => $selisih, 'harga_satuan' => (float) $p['harga_beli']],
            ], '', $keterangan);
            clear_old();
            flash('success', 'Penyesuaian stok tercatat (selisih ' . ($selisih > 0 ? '+' : '') . $selisih . ').');
            redirect('transaksi/penyesuaian.php');
        } catch (Throwable $e) {
            $errors[] = 'Gagal menyimpan penyesuaian.';
        }
    }

    foreach ($errors as $err) {
        flash('error', $err);
    }
    set_old($_POST);
    redirect('transaksi/penyesuaian.php');
}

$pageTitle = 'Penyesuaian Stok';
$pageScript = 'penyesuaian.js';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <p class="panel-description">Sesuaikan stok sistem dengan hasil hitung fisik (stok opname). Sistem mencatat selisihnya dan mengoreksi stok. Catatan wajib diisi.</p>

    <form method="post" action="<?= url('transaksi/penyesuaian.php') ?>" class="form" data-penyesuaian-form>
        <?= Csrf::field() ?>

        <div class="form-group item-product">
            <label>Barang</label>
            <input type="text" id="adj-search" class="item-search" placeholder="Ketik nama/kode..." autocomplete="off">
            <input type="hidden" name="id_produk" id="adj-id" class="item-id">
            <div class="item-suggestions" id="adj-suggestions"></div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Stok Sistem</label>
                <div class="item-stock-display" id="adj-current">-</div>
            </div>
            <div class="form-group">
                <label for="stok_fisik">Stok Fisik (hasil hitung)</label>
                <input type="number" id="stok_fisik" name="stok_fisik" min="0" step="any" required>
            </div>
        </div>

        <div class="form-group">
            <label for="keterangan">Catatan (wajib)</label>
            <textarea id="keterangan" name="keterangan" rows="3" required placeholder="Contoh: stok opname 08/10, ditemukan selisih karena kemasan rusak."><?= e(old('keterangan')) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Penyesuaian</button>
        </div>
    </form>
</div>

<?php clear_old(); require APP_ROOT . '/app/views/layouts/footer.php'; ?>
