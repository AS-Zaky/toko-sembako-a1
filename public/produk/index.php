<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

$produkModel = new Produk();
$search = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 15;

$totalItems = $produkModel->count($search);
$items = $produkModel->paginate($search, $perPage, page_offset($page, $perPage));

$pageTitle = 'Data Produk';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <div class="panel-toolbar">
        <form method="get" action="<?= url('produk/index.php') ?>" class="toolbar-search">
            <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari nama atau kode...">
            <button type="submit" class="btn btn-secondary">Cari</button>
        </form>
        <a href="<?= url('produk/tambah.php') ?>" class="btn btn-primary">+ Tambah Produk</a>
    </div>

    <?php if ($items === []): ?>
        <p class="empty-state">Produk tidak ditemukan.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th class="text-right">Harga Jual</th>
                    <th class="text-right">Stok</th>
                    <th class="text-right">Min.</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr class="<?= (float) $item['stok'] <= (float) $item['stok_minimum'] ? 'row-warning' : '' ?>">
                        <td><?= e($item['kode_produk']) ?></td>
                        <td><?= e($item['nama_produk']) ?></td>
                        <td><?= e($item['nama_kategori']) ?></td>
                        <td><?= e($item['satuan']) ?></td>
                        <td class="text-right"><?= e(format_rupiah($item['harga_jual'])) ?></td>
                        <td class="text-right"><?= e(rtrim(rtrim(number_format((float) $item['stok'], 2, ',', '.'), '0'), ',')) ?></td>
                        <td class="text-right"><?= e(rtrim(rtrim(number_format((float) $item['stok_minimum'], 2, ',', '.'), '0'), ',')) ?></td>
                        <td class="text-center">
                            <a href="<?= url('produk/edit.php?id=' . (int) $item['id_produk']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                            <form method="post" action="<?= url('produk/hapus.php') ?>" class="inline-form" data-confirm="Hapus produk &quot;<?= e($item['nama_produk']) ?>&quot;?">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= (int) $item['id_produk'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php
        $baseUrl = url('produk/index.php');
        $queryParams = ['q' => $search];
        require APP_ROOT . '/app/views/partials/pagination.php';
        ?>
    <?php endif; ?>
</div>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
