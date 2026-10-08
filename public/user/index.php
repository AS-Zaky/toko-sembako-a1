<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';
Auth::requireRole('admin');

$userModel = new User();
$search = trim((string) ($_GET['q'] ?? ''));
$items = $userModel->all($search);

$pageTitle = 'Data Pengguna';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<div class="panel">
    <div class="panel-toolbar">
        <form method="get" action="<?= url('user/index.php') ?>" class="toolbar-search">
            <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari nama atau username...">
            <button type="submit" class="btn btn-secondary">Cari</button>
        </form>
        <a href="<?= url('user/tambah.php') ?>" class="btn btn-primary">+ Tambah Pengguna</a>
    </div>

    <?php if ($items === []): ?>
        <p class="empty-state">Pengguna tidak ditemukan.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Peran</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['nama']) ?></td>
                        <td><?= e($item['username']) ?></td>
                        <td><?= e($item['role']) ?></td>
                        <td><span class="badge <?= (bool) $item['aktif'] ? 'badge-success' : 'badge-danger' ?>">
                            <?= (bool) $item['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                        </span></td>
                        <td class="text-center">
                            <a href="<?= url('user/edit.php?id=' . (int) $item['id_user']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                            <?php if ((int) $item['id_user'] !== Auth::id()): ?>
                                <form method="post" action="<?= url('user/hapus.php') ?>" class="inline-form" data-confirm="<?= (bool) $item['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?> akun &quot;<?= e($item['username']) ?>&quot;?">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $item['id_user'] ?>">
                                    <input type="hidden" name="aktif" value="<?= (bool) $item['aktif'] ? '0' : '1' ?>">
                                    <button type="submit" class="btn btn-sm <?= (bool) $item['aktif'] ? 'btn-danger' : 'btn-success' ?>">
                                        <?= (bool) $item['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
