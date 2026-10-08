<?php

declare(strict_types=1);

// Deactivate / reactivate an account (kept, not deleted, so history keeps its author).
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('user/index.php');
}

Csrf::verifyRequest();

$id = (int) ($_POST['id'] ?? 0);
$aktif = ($_POST['aktif'] ?? '1') === '1';

if ($id === Auth::id()) {
    flash('error', 'Anda tidak dapat mengubah status akun sendiri.');
    redirect('user/index.php');
}

$userModel = new User();
$user = $userModel->find($id);
if ($user === null) {
    flash('error', 'Pengguna tidak ditemukan.');
    redirect('user/index.php');
}

$userModel->setActive($id, $aktif);
flash('success', 'Akun "' . $user['username'] . '" ' . ($aktif ? 'diaktifkan.' : 'dinonaktifkan.'));
redirect('user/index.php');
