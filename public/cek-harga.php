<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
Auth::requireRole('admin', 'penjaga');

$pageTitle = 'Cek Harga & Stok';
$pageScript = 'cek-harga.js';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <p class="panel-description">Cari barang berdasarkan nama atau kode untuk melihat harga jual dan sisa stok.</p>
    <div class="form-group">
        <input type="text" id="lookup" placeholder="Ketik nama atau kode barang..." autocomplete="off" autofocus>
    </div>
    <div id="lookup-results" class="table-responsive">
        <p class="empty-state">Mulai mengetik untuk mencari.</p>
    </div>
</div>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
