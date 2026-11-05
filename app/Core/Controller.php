<?php
declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function view(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__) . '/Views/' . $name . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            throw new \RuntimeException('Vista no encontrada: ' . $name);
        }
        require $viewFile;
    }
}
