<?php

declare(strict_types=1);

/**
 * Shared helpers: escaping, URLs, redirects, flash messages, and formatting.
 */

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][$type][] = $message;
}

function get_flash(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);

    return $messages;
}

function format_rupiah($number): string
{
    return 'Rp ' . number_format((float) $number, 2, ',', '.');
}

function format_tanggal(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '-';
    }
    $ts = strtotime($datetime);

    return $ts === false ? '-' : date('d/m/Y H:i', $ts);
}

function format_tanggal_pendek(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '-';
    }
    $ts = strtotime($datetime);

    return $ts === false ? '-' : date('d/m/Y', $ts);
}

/** Human-readable stock availability for the public catalog (status only). */
function status_stok($stok, $stokMinimum): string
{
    $stok = (float) $stok;
    if ($stok <= 0) {
        return 'Habis';
    }
    if ($stok <= (float) $stokMinimum) {
        return 'Menipis';
    }

    return 'Tersedia';
}

/** Human-readable label for a transaction type. */
function label_jenis_transaksi(string $jenis): string
{
    return match ($jenis) {
        'masuk' => 'Barang Masuk',
        'keluar_terjual' => 'Terjual',
        'keluar_rusak' => 'Rusak',
        'keluar_kedaluwarsa' => 'Kedaluwarsa',
        'penyesuaian' => 'Penyesuaian Stok',
        default => $jenis,
    };
}

/** Clamp a page number and compute the OFFSET for paginated queries. */
function page_offset(int $page, int $perPage): int
{
    return max(0, ($page - 1) * $perPage);
}

/** Old-input helpers so forms keep values after a validation error. */
function set_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function old(string $key, string $default = ''): string
{
    $value = $_SESSION['_old'][$key] ?? $default;

    return is_string($value) ? $value : $default;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}
