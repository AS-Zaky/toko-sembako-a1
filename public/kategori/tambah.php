<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

$kategoriModel = new Kategori();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyRequest();

    $nama = trim((string) ($_POST['nama_kategori'] ?? ''));
    $deskripsi = trim((string) ($_POST['deskripsi'] ?? ''));

    if ($nama === '') {
        flash('error', 'Nama kategori wajib diisi.');
        set_old($_POST);
        redirect('kategori/tambah.php');
    }

    try {
        $kategoriModel->create($nama, $deskripsi);
        clear_old();
        flash('success', 'Kategori "' . $nama . '" ditambahkan.');
        redirect('kategori/index.php');
    } catch (Throwable $e) {
        flash('error', 'Nama kategori sudah digunakan.');
        set_old($_POST);
        redirect('kategori/tambah.php');
    }
}

$pageTitle = 'Tambah Kategori';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <form method="post" action="<?= url('kategori/tambah.php') ?>" class="form">
        <?= Csrf::field() ?>
        <div class="form-group">
            <label for="nama_kategori">Nama Kategori *</label>
            <input type="text" id="nama_kategori" name="nama_kategori" value="<?= e(old('nama_kategori')) ?>" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3"><?= e(old('deskripsi')) ?></textarea>
        </div>
        <div class="form-actions">
            <a href="<?= url('kategori/index.php') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Kategori</button>
        </div>
    </form>
</div>

<?php clear_old(); require APP_ROOT . '/app/views/layouts/footer.php'; ?>
