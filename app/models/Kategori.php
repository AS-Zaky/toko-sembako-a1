<?php

declare(strict_types=1);

/**
 * KATEGORI entity: category CRUD used by the product dropdown.
 */

final class Kategori
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(string $search = ''): array
    {
        if ($search !== '') {
            $stmt = $this->db->prepare(
                'SELECT k.*, (SELECT COUNT(*) FROM produk p WHERE p.id_kategori = k.id_kategori) AS jumlah_produk
                 FROM kategori k
                 WHERE k.nama_kategori ILIKE :q
                 ORDER BY k.nama_kategori'
            );
            $stmt->execute(['q' => '%' . $search . '%']);
        } else {
            $stmt = $this->db->query(
                'SELECT k.*, (SELECT COUNT(*) FROM produk p WHERE p.id_kategori = k.id_kategori) AS jumlah_produk
                 FROM kategori k ORDER BY k.nama_kategori'
            );
        }

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM kategori WHERE id_kategori = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function create(string $nama, string $deskripsi): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO kategori (nama_kategori, deskripsi) VALUES (:nama, :deskripsi) RETURNING id_kategori'
        );
        $stmt->execute(['nama' => $nama, 'deskripsi' => $deskripsi]);

        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, string $nama, string $deskripsi): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE kategori SET nama_kategori = :nama, deskripsi = :deskripsi WHERE id_kategori = :id'
        );

        return $stmt->execute(['nama' => $nama, 'deskripsi' => $deskripsi, 'id' => $id]);
    }

    /** Block deletion while products still use the category. */
    public function inUse(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM produk WHERE id_kategori = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetchColumn() !== false;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM kategori WHERE id_kategori = :id');

        return $stmt->execute(['id' => $id]);
    }
}
