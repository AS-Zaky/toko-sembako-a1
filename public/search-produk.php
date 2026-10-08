<?php

declare(strict_types=1);

// JSON endpoint used by the live product lookup (cek-harga and transaction forms).
require_once dirname(__DIR__) . '/app/bootstrap.php';
Auth::requireRole('admin', 'penjaga');

header('Content-Type: application/json; charset=utf-8');

$term = trim((string) ($_GET['q'] ?? ''));
if ($term === '') {
    echo json_encode([]);
    exit;
}

$results = (new Produk())->search($term, 10);

echo json_encode(array_map(static function (array $row): array {
    return [
        'id_produk' => (int) $row['id_produk'],
        'kode_produk' => $row['kode_produk'],
        'nama_produk' => $row['nama_produk'],
        'satuan' => $row['satuan'],
        'nama_kategori' => $row['nama_kategori'],
        'harga_jual' => (float) $row['harga_jual'],
        'harga_jual_format' => format_rupiah($row['harga_jual']),
        'stok' => (float) $row['stok'],
        'stok_minimum' => (float) $row['stok_minimum'],
        'status' => status_stok($row['stok'], $row['stok_minimum']),
    ];
}, $results));
