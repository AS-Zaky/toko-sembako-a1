<?php

declare(strict_types=1);

/**
 * TRANSAKSI entity: stock movements (in, out, adjustment), history, and reports.
 * Each movement is created inside one DB transaction so header, details,
 * and the stock trigger all commit or roll back together.
 */

final class Transaksi
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Record a stock movement.
     *
     * @param string $jenis       masuk | keluar_terjual | keluar_rusak | keluar_kedaluwarsa | penyesuaian
     * @param int    $idUser      the logged-in user
     * @param array  $items       list of ['id_produk' => int, 'jumlah' => float, 'harga_satuan' => float]
     * @param string $supplier    supplier name (goods in only)
     * @param string $keterangan  note (mandatory for penyesuaian)
     */
    public function create(string $jenis, int $idUser, array $items, string $supplier = '', string $keterangan = ''): int
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += (float) $item['jumlah'] * (float) $item['harga_satuan'];
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO transaksi (id_user, jenis_transaksi, tanggal_transaksi, total_harga, supplier, keterangan)
                 VALUES (:id_user, :jenis, NOW(), :total, :supplier, :keterangan)
                 RETURNING id_transaksi'
            );
            $stmt->execute([
                'id_user' => $idUser,
                'jenis' => $jenis,
                'total' => $total,
                'supplier' => $supplier !== '' ? $supplier : null,
                'keterangan' => $keterangan !== '' ? $keterangan : null,
            ]);
            $idTransaksi = (int) $stmt->fetchColumn();

            $detailStmt = $this->db->prepare(
                'INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_satuan, subtotal)
                 VALUES (:id_transaksi, :id_produk, :jumlah, :harga_satuan, :subtotal)'
            );
            foreach ($items as $item) {
                $jumlah = (float) $item['jumlah'];
                $harga = (float) $item['harga_satuan'];
                $detailStmt->execute([
                    'id_transaksi' => $idTransaksi,
                    'id_produk' => (int) $item['id_produk'],
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga,
                    'subtotal' => $jumlah * $harga,
                ]);
            }

            $this->db->commit();

            return $idTransaksi;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, u.nama AS nama_user
             FROM transaksi t JOIN users u ON u.id_user = t.id_user
             WHERE t.id_transaksi = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /** Recent movements for the dashboard. */
    public function recent(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.id_transaksi, t.jenis_transaksi, t.tanggal_transaksi, t.total_harga, u.nama AS nama_user
             FROM transaksi t JOIN users u ON u.id_user = t.id_user
             ORDER BY t.tanggal_transaksi DESC, t.id_transaksi DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Full movement history with optional filters. */
    public function history(string $jenis = '', string $dari = '', string $sampai = '', int $limit = 200): array
    {
        $sql = 'SELECT t.id_transaksi, t.jenis_transaksi, t.tanggal_transaksi, t.total_harga,
                       t.supplier, t.keterangan, u.nama AS nama_user
                FROM transaksi t JOIN users u ON u.id_user = t.id_user
                WHERE 1=1';
        $params = [];
        if ($jenis !== '') {
            $sql .= ' AND t.jenis_transaksi = :jenis';
            $params['jenis'] = $jenis;
        }
        if ($dari !== '') {
            $sql .= ' AND t.tanggal_transaksi >= :dari';
            $params['dari'] = $dari . ' 00:00:00';
        }
        if ($sampai !== '') {
            $sql .= ' AND t.tanggal_transaksi <= :sampai';
            $params['sampai'] = $sampai . ' 23:59:59';
        }
        $sql .= ' ORDER BY t.tanggal_transaksi DESC, t.id_transaksi DESC LIMIT :limit';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Inventory report per product for a period:
     * totals in, out (sold/damaged/expired), and adjustments.
     */
    public function report(string $dari, string $sampai): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.kode_produk, p.nama_produk, p.satuan, p.stok,
                    COALESCE(SUM(CASE WHEN t.jenis_transaksi = 'masuk' THEN d.jumlah END), 0) AS total_masuk,
                    COALESCE(SUM(CASE WHEN t.jenis_transaksi = 'keluar_terjual' THEN d.jumlah END), 0) AS total_terjual,
                    COALESCE(SUM(CASE WHEN t.jenis_transaksi = 'keluar_rusak' THEN d.jumlah END), 0) AS total_rusak,
                    COALESCE(SUM(CASE WHEN t.jenis_transaksi = 'keluar_kedaluwarsa' THEN d.jumlah END), 0) AS total_kedaluwarsa,
                    COALESCE(SUM(CASE WHEN t.jenis_transaksi = 'penyesuaian' THEN d.jumlah END), 0) AS total_penyesuaian
             FROM produk p
             LEFT JOIN detail_transaksi d ON d.id_produk = p.id_produk
             LEFT JOIN transaksi t ON t.id_transaksi = d.id_transaksi
                 AND t.tanggal_transaksi >= :dari
                 AND t.tanggal_transaksi <= :sampai
             GROUP BY p.id_produk, p.kode_produk, p.nama_produk, p.satuan, p.stok
             ORDER BY p.nama_produk"
        );
        $stmt->execute([
            'dari' => $dari . ' 00:00:00',
            'sampai' => $sampai . ' 23:59:59',
        ]);

        return $stmt->fetchAll();
    }

    public function countToday(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM transaksi WHERE tanggal_transaksi::date = CURRENT_DATE');

        return (int) $stmt->fetchColumn();
    }
}
