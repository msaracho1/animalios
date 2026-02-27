<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    private static string $viewsPath;

    public static function init(string $viewsPath): void
    {
        self::$viewsPath = rtrim($viewsPath, '/\\');
    }

    public static function render(string $view, array $data = []): void
    {
        $file = self::viewFile($view);
        if (!is_file($file)) {
            throw new \RuntimeException("View not found: $view ($file)");
        }

        extract($data, EXTR_SKIP);
        require $file;
    }

    private static function viewFile(string $view): string
    {
        $rel = str_replace('.', DIRECTORY_SEPARATOR, $view);
        return self::$viewsPath . DIRECTORY_SEPARATOR . $rel . '.php';
    }
}