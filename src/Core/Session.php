<?php
declare(strict_types=1);

namespace App\Core;

final class Session
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['_flash'] ??= [];
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(16));
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key)
    {
        if (!array_key_exists($key, $_SESSION['_flash'])) return null;
        $v = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        return $v;
    }

    public static function csrfToken(): string
    {
        return (string)($_SESSION['_csrf'] ?? '');
    }

    public static function verifyCsrf(?string $token): bool
    {
        return is_string($token) && hash_equals(self::csrfToken(), $token);
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(16));
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
