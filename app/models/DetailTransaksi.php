<?php

declare(strict_types=1);

/**
 * DETAIL_TRANSAKSI entity: the product lines of each stock movement.
 */

final class DetailTransaksi
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** All product lines belonging to one transaction. */
    public function forTransaksi(int $idTransaksi): array
    {
        $stmt = $this->db->prepare(
            'SELECT d.*, p.kode_produk, p.nama_produk, p.satuan
             FROM detail_transaksi d
             JOIN produk p ON p.id_produk = d.id_produk
             WHERE d.id_transaksi = :id
             ORDER BY d.id_detail'
        );
        $stmt->execute(['id' => $idTransaksi]);

        return $stmt->fetchAll();
    }
}
