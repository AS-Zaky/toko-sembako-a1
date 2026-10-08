<?php
/** Sidebar navigation, filtered by role. */
$role = Auth::role();
$current = $_SERVER['REQUEST_URI'] ?? '';
$isActive = static function (string $needle) use ($current): string {
    return str_contains($current, $needle) ? 'active' : '';
};
?>
<aside class="sidebar" data-sidebar>
    <div class="sidebar-brand">
        <span class="sidebar-brand-name"><?= e(APP_NAME) ?></span>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= url('dashboard.php') ?>" class="<?= $isActive('dashboard') ?>">Dashboard</a>
        <a href="<?= url('cek-harga.php') ?>" class="<?= $isActive('cek-harga') ?>">Cek Harga &amp; Stok</a>

        <div class="sidebar-section">Transaksi</div>
        <a href="<?= url('transaksi/masuk.php') ?>" class="<?= $isActive('masuk') ?>">Barang Masuk</a>
        <a href="<?= url('transaksi/keluar.php') ?>" class="<?= $isActive('keluar') ?>">Barang Keluar</a>
        <?php if ($role === 'admin'): ?>
            <a href="<?= url('transaksi/penyesuaian.php') ?>" class="<?= $isActive('penyesuaian') ?>">Penyesuaian Stok</a>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
            <div class="sidebar-section">Data Master</div>
            <a href="<?= url('produk/index.php') ?>" class="<?= $isActive('produk') ?>">Produk</a>
            <a href="<?= url('kategori/index.php') ?>" class="<?= $isActive('kategori') ?>">Kategori</a>

            <div class="sidebar-section">Laporan</div>
            <a href="<?= url('riwayat/index.php') ?>" class="<?= $isActive('riwayat') ?>">Riwayat Stok</a>
            <a href="<?= url('laporan/index.php') ?>" class="<?= $isActive('laporan') ?>">Laporan</a>

            <div class="sidebar-section">Pengaturan</div>
            <a href="<?= url('user/index.php') ?>" class="<?= $isActive('user') ?>">Pengguna</a>
        <?php endif; ?>

        <div class="sidebar-section">Lainnya</div>
        <a href="<?= url('katalog.php') ?>" target="_blank">Katalog Publik</a>
        <a href="<?= url('logout.php') ?>">Keluar</a>
    </nav>
</aside>
<div class="sidebar-overlay" data-sidebar-overlay></div>
