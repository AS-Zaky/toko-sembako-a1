<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

$produkModel = new Produk();
$kategoriModel = new Kategori();
$kategoris = $kategoriModel->all();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyRequest();

    $data = [
        'id_kategori' => (int) ($_POST['id_kategori'] ?? 0),
        'kode_produk' => trim((string) ($_POST['kode_produk'] ?? '')),
        'nama_produk' => trim((string) ($_POST['nama_produk'] ?? '')),
        'satuan' => trim((string) ($_POST['satuan'] ?? '')),
        'harga_beli' => (float) str_replace(',', '.', (string) ($_POST['harga_beli'] ?? '0')),
        'harga_jual' => (float) str_replace(',', '.', (string) ($_POST['harga_jual'] ?? '0')),
        'stok' => (float) str_replace(',', '.', (string) ($_POST['stok'] ?? '0')),
        'stok_minimum' => (float) str_replace(',', '.', (string) ($_POST['stok_minimum'] ?? '0')),
    ];

    $errors = [];
    if ($data['kode_produk'] === '' || $data['nama_produk'] === '' || $data['satuan'] === '') {
        $errors[] = 'Kode, nama, dan satuan wajib diisi.';
    }
    if ($data['id_kategori'] <= 0 || $kategoriModel->find($data['id_kategori']) === null) {
        $errors[] = 'Pilih kategori yang valid.';
    }
    if ($data['kode_produk'] !== '' && $produkModel->kodeExists($data['kode_produk'])) {
        $errors[] = 'Kode produk sudah digunakan.';
    }
    foreach (['harga_beli', 'harga_jual', 'stok', 'stok_minimum'] as $field) {
        if ($data[$field] < 0) {
            $errors[] = 'Harga dan stok tidak boleh negatif.';
            break;
        }
    }

    if ($errors === []) {
        $produkModel->create($data);
        clear_old();
        flash('success', 'Produk "' . $data['nama_produk'] . '" ditambahkan.');
        redirect('produk/index.php');
    }

    foreach ($errors as $err) {
        flash('error', $err);
    }
    set_old($_POST);
    redirect('produk/tambah.php');
}

$pageTitle = 'Tambah Produk';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <form method="post" action="<?= url('produk/tambah.php') ?>" class="form">
        <?= Csrf::field() ?>

        <div class="form-row">
            <div class="form-group">
                <label for="kode_produk">Kode Produk *</label>
                <input type="text" id="kode_produk" name="kode_produk" value="<?= e(old('kode_produk')) ?>" required>
            </div>
            <div class="form-group">
                <label for="nama_produk">Nama Produk *</label>
                <input type="text" id="nama_produk" name="nama_produk" value="<?= e(old('nama_produk')) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_kategori">Kategori *</label>
                <select id="id_kategori" name="id_kategori" required>
                    <option value="">- Pilih kategori -</option>
                    <?php foreach ($kategoris as $k): ?>
                        <option value="<?= (int) $k['id_kategori'] ?>" <?= old('id_kategori') == $k['id_kategori'] ? 'selected' : '' ?>>
                            <?= e($k['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="satuan">Satuan *</label>
                <input type="text" id="satuan" name="satuan" value="<?= e(old('satuan')) ?>" placeholder="pcs, kg, bungkus..." required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="harga_beli">Harga Beli</label>
                <input type="number" id="harga_beli" name="harga_beli" value="<?= e(old('harga_beli', '0')) ?>" min="0" step="any">
            </div>
            <div class="form-group">
                <label for="harga_jual">Harga Jual</label>
                <input type="number" id="harga_jual" name="harga_jual" value="<?= e(old('harga_jual', '0')) ?>" min="0" step="any">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="stok">Stok Awal</label>
                <input type="number" id="stok" name="stok" value="<?= e(old('stok', '0')) ?>" min="0" step="any">
            </div>
            <div class="form-group">
                <label for="stok_minimum">Stok Minimum</label>
                <input type="number" id="stok_minimum" name="stok_minimum" value="<?= e(old('stok_minimum', '0')) ?>" min="0" step="any">
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= url('produk/index.php') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </div>
    </form>
</div>

<?php clear_old(); require APP_ROOT . '/app/views/layouts/footer.php'; ?>
