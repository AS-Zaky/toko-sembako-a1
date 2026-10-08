<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

$kategoriModel = new Kategori();
$search = trim((string) ($_GET['q'] ?? ''));
$items = $kategoriModel->all($search);

$pageTitle = 'Data Kategori';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <div class="panel-toolbar">
        <form method="get" action="<?= url('kategori/index.php') ?>" class="toolbar-search">
            <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari kategori...">
            <button type="submit" class="btn btn-secondary">Cari</button>
        </form>
        <a href="<?= url('kategori/tambah.php') ?>" class="btn btn-primary">+ Tambah Kategori</a>
    </div>

    <?php if ($items === []): ?>
        <p class="empty-state">Kategori tidak ditemukan.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-right">Jumlah Produk</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['nama_kategori']) ?></td>
                        <td><?= e($item['deskripsi'] ?? '-') ?></td>
                        <td class="text-right"><?= (int) $item['jumlah_produk'] ?></td>
                        <td class="text-center">
                            <a href="<?= url('kategori/edit.php?id=' . (int) $item['id_kategori']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                            <form method="post" action="<?= url('kategori/hapus.php') ?>" class="inline-form" data-confirm="Hapus kategori &quot;<?= e($item['nama_kategori']) ?>&quot;?">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= (int) $item['id_kategori'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
