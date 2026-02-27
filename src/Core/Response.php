<?php
declare(strict_types=1);

namespace App\Core;

final class Response
{
    public static function redirect(string $to): void
    {
        header('Location: ' . $to);
        exit;
    }

    public static function back(): void
    {
        self::redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}
