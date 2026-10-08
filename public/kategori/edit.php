<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

$kategoriModel = new Kategori();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$kategori = $kategoriModel->find($id);

if ($kategori === null) {
    flash('error', 'Kategori tidak ditemukan.');
    redirect('kategori/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyRequest();

    $nama = trim((string) ($_POST['nama_kategori'] ?? ''));
    $deskripsi = trim((string) ($_POST['deskripsi'] ?? ''));

    if ($nama === '') {
        flash('error', 'Nama kategori wajib diisi.');
    } else {
        try {
            $kategoriModel->update($id, $nama, $deskripsi);
            flash('success', 'Kategori diperbarui.');
            redirect('kategori/index.php');
        } catch (Throwable $e) {
            flash('error', 'Nama kategori sudah digunakan.');
        }
    }

    $kategori = array_merge($kategori, ['nama_kategori' => $nama, 'deskripsi' => $deskripsi]);
}

$pageTitle = 'Edit Kategori';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <form method="post" action="<?= url('kategori/edit.php') ?>" class="form">
        <?= Csrf::field() ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <div class="form-group">
            <label for="nama_kategori">Nama Kategori *</label>
            <input type="text" id="nama_kategori" name="nama_kategori" value="<?= e($kategori['nama_kategori']) ?>" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3"><?= e($kategori['deskripsi'] ?? '') ?></textarea>
        </div>
        <div class="form-actions">
            <a href="<?= url('kategori/index.php') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
