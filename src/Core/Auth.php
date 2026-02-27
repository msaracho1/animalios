<?php
declare(strict_types=1);

namespace App\Core;

use App\Repositories\UserRepository;

final class Auth
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public static function check(): bool
    {
        return Session::get('user_id') !== null;
    }

    public static function id(): ?int
    {
        $id = Session::get('user_id');
        return $id === null ? null : (int)$id;
    }

    public static function user(): ?object
    {
        $id = self::id();
        if (!$id) return null;

        // cache in session for this request
        static $cached = null;
        static $cachedId = null;
        if ($cached && $cachedId === $id) return $cached;

        $repo = new UserRepository();
        $cached = $repo->findWithRole($id);
        $cachedId = $id;
        return $cached;
    }

    public static function userOrFail(): object
    {
        $u = self::user();
        if (!$u) {
            http_response_code(401);
            echo '401 - Unauthorized';
            exit;
        }
        return $u;
    }

    public static function login(int $userId): void
    {
        Session::regenerate();
        Session::put('user_id', $userId);
    }

    public static function logout(): void
    {
        Session::forget('user_id');
        Session::regenerate();
    }
}
