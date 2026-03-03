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

    public static function notFound(
        string $message = 'La URL solicitada no existe o no tenés permisos para acceder.',
        string $title = 'URL no encontrada'
    ): void {
        http_response_code(404);
        View::render('errors.default', [
            'title' => $title,
            'statusCode' => 404,
            'message' => $message,
        ]);
    }
}
