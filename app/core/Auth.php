<?php

declare(strict_types=1);

/**
 * Session-based authentication and role checks.
 * Every protected page starts with require_login() and, when needed, require_role().
 */

final class Auth
{
    /** Attempt a login; on success regenerates the session and stores the user. */
    public static function attempt(string $username, string $password): bool
    {
        $user = (new User())->findByUsername($username);
        if ($user === null || !(bool) $user['aktif']) {
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id_user' => (int) $user['id_user'],
            'nama' => $user['nama'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];

        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user']) ? (int) $_SESSION['user']['id_user'] : null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    /** Send unauthenticated visitors to the login page. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('error', 'Silakan masuk terlebih dahulu.');
            redirect('login.php');
        }
    }

    /** Restrict a page to specific roles; others get a 403. */
    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();
        if (!in_array(self::role(), $roles, true)) {
            http_response_code(403);
            exit('403 - Anda tidak memiliki akses ke halaman ini.');
        }
    }
}
