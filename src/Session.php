<?php
namespace App;

/**
 * Session helper. Wraps PHP's $_SESSION superglobal so that the rest of
 * the codebase doesn't have to think about session_start() or array keys.
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function forget(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function userId(): ?int
    {
        $id = self::get('user_id');
        return $id ? (int)$id : null;
    }

    public static function role(): ?string
    {
        return self::get('role');
    }

    public static function isLoggedIn(): bool
    {
        return self::userId() !== null;
    }
}
