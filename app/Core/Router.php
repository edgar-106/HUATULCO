<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $action): void
    {
        $this->routes['GET'][$this->normalize($path)] = $action;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $base = rtrim((require dirname(__DIR__, 2) . '/config/app.php')['base_url'], '/');
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        $action = $this->routes[strtoupper($method)][$this->normalize($path)] ?? null;
        if ($action === null) {
            http_response_code(404);
            echo 'Página no encontrada';
            return;
        }

        [$controller, $handler] = $action;
        (new $controller())->$handler();
    }

    private function normalize(string $path): string
    {
        $normalized = '/' . trim($path, '/');
        return $normalized === '/' ? '/' : rtrim($normalized, '/');
    }
}
