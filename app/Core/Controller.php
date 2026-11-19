<?php
declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function adminDatabase(): ?\PDO
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/cuenta/ingresar');
            return null;
        }
        $db = Database::connection();
        $query = $db->prepare('SELECT role FROM users WHERE id = ?');
        $query->execute([$_SESSION['user_id']]);
        if ($query->fetchColumn() !== 'admin') {
            http_response_code(403);
            echo 'Acceso reservado a administradores.';
            return null;
        }
        return $db;
    }

    protected function redirect(string $path): void
    {
        $app = require dirname(__DIR__, 2) . '/config/app.php';
        header('Location: ' . $app['base_url'] . $path, true, 303);
    }

    protected function verifyToken(): bool
    {
        $token = $_POST['token'] ?? null;
        if (!is_string($token) || !isset($_SESSION['token']) || !hash_equals($_SESSION['token'], $token)) {
            http_response_code(403);
            echo 'Solicitud no válida.';
            return false;
        }
        return true;
    }

    protected function view(string $name, array $data = []): void
    {
        $token = $_SESSION['token'] ??= bin2hex(random_bytes(32));
        $e = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        extract($data, EXTR_SKIP);
        $app ??= require dirname(__DIR__, 2) . '/config/app.php';
        $viewFile = dirname(__DIR__) . '/Views/' . $name . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            throw new \RuntimeException('Vista no encontrada: ' . $name);
        }
        require $viewFile;
    }
}
