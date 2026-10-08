<?php

declare(strict_types=1);

/**
 * USER entity: login lookup and account management.
 */

final class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id_user, nama, username, role, aktif FROM users WHERE id_user = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /** All accounts, optionally filtered by a search term. */
    public function all(string $search = ''): array
    {
        if ($search !== '') {
            $stmt = $this->db->prepare(
                'SELECT id_user, nama, username, role, aktif FROM users
                 WHERE nama ILIKE :q OR username ILIKE :q
                 ORDER BY nama'
            );
            $stmt->execute(['q' => '%' . $search . '%']);
        } else {
            $stmt = $this->db->query('SELECT id_user, nama, username, role, aktif FROM users ORDER BY nama');
        }

        return $stmt->fetchAll();
    }

    public function create(string $nama, string $username, string $password, string $role): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (nama, username, password, role)
             VALUES (:nama, :username, :password, :role)
             RETURNING id_user'
        );
        $stmt->execute([
            'nama' => $nama,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /** Update profile; pass a non-empty password to reset it. */
    public function update(int $id, string $nama, string $username, string $role, ?string $password = null): bool
    {
        if ($password !== null && $password !== '') {
            $stmt = $this->db->prepare(
                'UPDATE users SET nama = :nama, username = :username, role = :role, password = :password
                 WHERE id_user = :id'
            );
            return $stmt->execute([
                'nama' => $nama,
                'username' => $username,
                'role' => $role,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'id' => $id,
            ]);
        }

        $stmt = $this->db->prepare(
            'UPDATE users SET nama = :nama, username = :username, role = :role WHERE id_user = :id'
        );

        return $stmt->execute(['nama' => $nama, 'username' => $username, 'role' => $role, 'id' => $id]);
    }

    /** Deactivate instead of deleting so history keeps its author. */
    public function setActive(int $id, bool $aktif): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET aktif = :aktif WHERE id_user = :id');

        return $stmt->execute(['aktif' => $aktif, 'id' => $id]);
    }

    public function usernameExists(string $username, ?int $exceptId = null): bool
    {
        if ($exceptId !== null) {
            $stmt = $this->db->prepare('SELECT 1 FROM users WHERE username = :u AND id_user <> :id LIMIT 1');
            $stmt->execute(['u' => $username, 'id' => $exceptId]);
        } else {
            $stmt = $this->db->prepare('SELECT 1 FROM users WHERE username = :u LIMIT 1');
            $stmt->execute(['u' => $username]);
        }

        return $stmt->fetchColumn() !== false;
    }
}
