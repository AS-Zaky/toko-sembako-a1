<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyRequest();

    $nama = trim((string) ($_POST['nama'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $role = (string) ($_POST['role'] ?? 'penjaga');

    $errors = [];
    if ($nama === '' || $username === '') {
        $errors[] = 'Nama dan username wajib diisi.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Kata sandi minimal 6 karakter.';
    }
    if (!in_array($role, ['admin', 'penjaga'], true)) {
        $errors[] = 'Peran tidak valid.';
    }
    if ($username !== '' && $userModel->usernameExists($username)) {
        $errors[] = 'Username sudah digunakan.';
    }

    if ($errors === []) {
        $userModel->create($nama, $username, $password, $role);
        clear_old();
        flash('success', 'Akun "' . $username . '" dibuat.');
        redirect('user/index.php');
    }

    foreach ($errors as $err) {
        flash('error', $err);
    }
    set_old($_POST);
    redirect('user/tambah.php');
}

$pageTitle = 'Tambah Pengguna';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <form method="post" action="<?= url('user/tambah.php') ?>" class="form">
        <?= Csrf::field() ?>
        <div class="form-row">
            <div class="form-group">
                <label for="nama">Nama *</label>
                <input type="text" id="nama" name="nama" value="<?= e(old('nama')) ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" value="<?= e(old('username')) ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="password">Kata Sandi *</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label for="role">Peran *</label>
                <select id="role" name="role">
                    <option value="penjaga" <?= old('role', 'penjaga') === 'penjaga' ? 'selected' : '' ?>>Penjaga</option>
                    <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <a href="<?= url('user/index.php') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
        </div>
    </form>
</div>

<?php clear_old(); require APP_ROOT . '/app/views/layouts/footer.php'; ?>
