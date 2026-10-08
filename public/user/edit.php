<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

$userModel = new User();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$user = $userModel->find($id);

if ($user === null) {
    flash('error', 'Pengguna tidak ditemukan.');
    redirect('user/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyRequest();

    $nama = trim((string) ($_POST['nama'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $role = (string) ($_POST['role'] ?? $user['role']);

    $errors = [];
    if ($nama === '' || $username === '') {
        $errors[] = 'Nama dan username wajib diisi.';
    }
    if (!in_array($role, ['admin', 'penjaga'], true)) {
        $errors[] = 'Peran tidak valid.';
    }
    if ($userModel->usernameExists($username, $id)) {
        $errors[] = 'Username sudah digunakan.';
    }
    if ($password !== '' && strlen($password) < 6) {
        $errors[] = 'Kata sandi minimal 6 karakter.';
    }

    if ($errors === []) {
        $userModel->update($id, $nama, $username, $role, $password !== '' ? $password : null);
        flash('success', 'Akun diperbarui.');
        redirect('user/index.php');
    }

    foreach ($errors as $err) {
        flash('error', $err);
    }
    $user = array_merge($user, ['nama' => $nama, 'username' => $username, 'role' => $role]);
}

$pageTitle = 'Edit Pengguna';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <form method="post" action="<?= url('user/edit.php') ?>" class="form">
        <?= Csrf::field() ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <div class="form-row">
            <div class="form-group">
                <label for="nama">Nama *</label>
                <input type="text" id="nama" name="nama" value="<?= e($user['nama']) ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" value="<?= e($user['username']) ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="password">Kata Sandi Baru (kosongkan jika tidak diubah)</label>
                <input type="password" id="password" name="password" minlength="6">
            </div>
            <div class="form-group">
                <label for="role">Peran *</label>
                <select id="role" name="role">
                    <option value="penjaga" <?= $user['role'] === 'penjaga' ? 'selected' : '' ?>>Penjaga</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <a href="<?= url('user/index.php') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
