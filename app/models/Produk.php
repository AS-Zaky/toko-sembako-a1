<?php

declare(strict_types=1);

/**
 * PRODUK entity: product CRUD, search, low-stock query, and the public catalog.
 */

final class Produk
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Paginated product list with optional search by name or code. */
    public function paginate(string $search, int $perPage, int $offset): array
    {
        if ($search !== '') {
            $stmt = $this->db->prepare(
                'SELECT p.*, k.nama_kategori
                 FROM produk p JOIN kategori k ON k.id_kategori = p.id_kategori
                 WHERE p.nama_produk ILIKE :q OR p.kode_produk ILIKE :q
                 ORDER BY p.nama_produk
                 LIMIT :limit OFFSET :offset'
            );
            $stmt->bindValue('q', '%' . $search . '%');
        } else {
            $stmt = $this->db->prepare(
                'SELECT p.*, k.nama_kategori
                 FROM produk p JOIN kategori k ON k.id_kategori = p.id_kategori
                 ORDER BY p.nama_produk
                 LIMIT :limit OFFSET :offset'
            );
        }
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(string $search = ''): int
    {
        if ($search !== '') {
            $stmt = $this->db->prepare(
                'SELECT COUNT(*) FROM produk WHERE nama_produk ILIKE :q OR kode_produk ILIKE :q'
            );
            $stmt->execute(['q' => '%' . $search . '%']);
        } else {
            $stmt = $this->db->query('SELECT COUNT(*) FROM produk');
        }

        return (int) $stmt->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, k.nama_kategori
             FROM produk p JOIN kategori k ON k.id_kategori = p.id_kategori
             WHERE p.id_produk = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO produk (id_kategori, kode_produk, nama_produk, satuan, harga_beli, harga_jual, stok, stok_minimum)
             VALUES (:id_kategori, :kode_produk, :nama_produk, :satuan, :harga_beli, :harga_jual, :stok, :stok_minimum)
             RETURNING id_produk'
        );
        $stmt->execute($data);

        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        $data['id_produk'] = $id;
        $stmt = $this->db->prepare(
            'UPDATE produk SET
                id_kategori = :id_kategori,
                kode_produk = :kode_produk,
                nama_produk = :nama_produk,
                satuan = :satuan,
                harga_beli = :harga_beli,
                harga_jual = :harga_jual,
                stok = :stok,
                stok_minimum = :stok_minimum
             WHERE id_produk = :id_produk'
        );

        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM produk WHERE id_produk = :id');

        return $stmt->execute(['id' => $id]);
    }

    public function kodeExists(string $kode, ?int $exceptId = null): bool
    {
        if ($exceptId !== null) {
            $stmt = $this->db->prepare('SELECT 1 FROM produk WHERE kode_produk = :k AND id_produk <> :id LIMIT 1');
            $stmt->execute(['k' => $kode, 'id' => $exceptId]);
        } else {
            $stmt = $this->db->prepare('SELECT 1 FROM produk WHERE kode_produk = :k LIMIT 1');
            $stmt->execute(['k' => $kode]);
        }

        return $stmt->fetchColumn() !== false;
    }

    /** Fast lookup by name or code for the price/stock page and transaction forms. */
    public function search(string $term, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.id_produk, p.kode_produk, p.nama_produk, p.satuan, p.harga_jual, p.stok, p.stok_minimum,
                    k.nama_kategori
             FROM produk p JOIN kategori k ON k.id_kategori = p.id_kategori
             WHERE p.nama_produk ILIKE :q OR p.kode_produk ILIKE :q
             ORDER BY p.nama_produk
             LIMIT :limit'
        );
        $stmt->bindValue('q', '%' . $term . '%');
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** Items at or below their minimum stock. */
    public function lowStock(): array
    {
        $stmt = $this->db->query(
            'SELECT p.*, k.nama_kategori
             FROM produk p JOIN kategori k ON k.id_kategori = p.id_kategori
             WHERE p.stok <= p.stok_minimum
             ORDER BY p.stok ASC, p.nama_produk'
        );

        return $stmt->fetchAll();
    }

    public function countLowStock(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM produk WHERE stok <= stok_minimum');

        return (int) $stmt->fetchColumn();
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM produk')->fetchColumn();
    }

    /** Public catalog: status only, never purchase price. */
    public function catalog(string $search = ''): array
    {
        if ($search !== '') {
            $stmt = $this->db->prepare(
                'SELECT p.kode_produk, p.nama_produk, p.satuan, p.harga_jual, p.stok, p.stok_minimum, k.nama_kategori
                 FROM produk p JOIN kategori k ON k.id_kategori = p.id_kategori
                 WHERE p.nama_produk ILIKE :q OR p.kode_produk ILIKE :q
                 ORDER BY p.nama_produk'
            );
            $stmt->execute(['q' => '%' . $search . '%']);
        } else {
            $stmt = $this->db->query(
                'SELECT p.kode_produk, p.nama_produk, p.satuan, p.harga_jual, p.stok, p.stok_minimum, k.nama_kategori
                 FROM produk p JOIN kategori k ON k.id_kategori = p.id_kategori
                 ORDER BY p.nama_produk'
            );
        }

        return $stmt->fetchAll();
    }
}
