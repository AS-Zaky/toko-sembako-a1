<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('produk/index.php');
}

Csrf::verifyRequest();

$id = (int) ($_POST['id'] ?? 0);
$produkModel = new Produk();
$produk = $produkModel->find($id);

if ($produk === null) {
    flash('error', 'Produk tidak ditemukan.');
    redirect('produk/index.php');
}

try {
    $produkModel->delete($id);
    flash('success', 'Produk "' . $produk['nama_produk'] . '" dihapus.');
} catch (Throwable $e) {
    flash('error', 'Produk tidak dapat dihapus karena sudah tercatat dalam transaksi.');
}

redirect('produk/index.php');
