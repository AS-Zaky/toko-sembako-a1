<?php
/** Flash messages (success / error). */
$flashes = get_flash();
foreach (['success', 'error'] as $type):
    foreach ($flashes[$type] ?? [] as $message): ?>
        <div class="alert alert-<?= e($type) ?>"><?= e($message) ?></div>
    <?php endforeach;
endforeach;
