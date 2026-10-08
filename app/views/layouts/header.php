<?php
/** Shared header. Set $pageTitle before including. */
$pageTitle = $pageTitle ?? APP_NAME;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="app">
    <?php require APP_ROOT . '/app/views/layouts/sidebar.php'; ?>
    <div class="main">
        <header class="topbar">
            <button type="button" class="menu-toggle" data-menu-toggle aria-label="Buka menu">&#9776;</button>
            <h1 class="topbar-title"><?= e($pageTitle) ?></h1>
            <div class="topbar-user">
                <span class="topbar-user-name"><?= e($user['nama'] ?? '') ?></span>
                <span class="topbar-user-role"><?= e($user['role'] ?? '') ?></span>
            </div>
        </header>
        <main class="content">
            <?php require APP_ROOT . '/app/views/partials/alert.php'; ?>
