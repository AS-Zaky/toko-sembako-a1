<?php

declare(strict_types=1);

/**
 * CSRF token creation and verification.
 * Every POST form includes the token; it is checked before any state change.
 */

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }

    public static function verify(?string $token): bool
    {
        return is_string($token)
            && isset($_SESSION[self::SESSION_KEY])
            && hash_equals($_SESSION[self::SESSION_KEY], $token);
    }

    /** Verify the posted token or stop the request. */
    public static function verifyRequest(): void
    {
        if (!self::verify($_POST['_token'] ?? null)) {
            http_response_code(419);
            exit('419 - Sesi kedaluwarsa. Silakan muat ulang halaman.');
        }
    }
}
