<?php
/**
 * Pagination links.
 * Expects: $totalItems, $perPage, $page, $baseUrl (query string preserved via $queryParams).
 */
$totalPages = max(1, (int) ceil(($totalItems ?? 0) / max(1, $perPage ?? 1)));
$page = max(1, min($page ?? 1, $totalPages));
if ($totalPages > 1):
    $queryParams = $queryParams ?? [];
    $buildUrl = static function (int $p) use ($queryParams, $baseUrl): string {
        $queryParams['page'] = $p;

        return $baseUrl . '?' . http_build_query($queryParams);
    };
    ?>
    <nav class="pagination" aria-label="Navigasi halaman">
        <?php if ($page > 1): ?>
            <a class="pagination-link" href="<?= e($buildUrl($page - 1)) ?>">&laquo; Sebelumnya</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === $page): ?>
                <span class="pagination-link active"><?= $i ?></span>
            <?php else: ?>
                <a class="pagination-link" href="<?= e($buildUrl($i)) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a class="pagination-link" href="<?= e($buildUrl($page + 1)) ?>">Berikutnya &raquo;</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>
