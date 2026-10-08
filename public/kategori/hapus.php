<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('kategori/index.php');
}

Csrf::verifyRequest();

$id = (int) ($_POST['id'] ?? 0);
$kategoriModel = new Kategori();
$kategori = $kategoriModel->find($id);

if ($kategori === null) {
    flash('error', 'Kategori tidak ditemukan.');
    redirect('kategori/index.php');
}

if ($kategoriModel->inUse($id)) {
    flash('error', 'Kategori tidak dapat dihapus karena masih dipakai produk.');
    redirect('kategori/index.php');
}

$kategoriModel->delete($id);
flash('success', 'Kategori "' . $kategori['nama_kategori'] . '" dihapus.');
redirect('kategori/index.php');
