<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDOException;

final class AccountController extends Controller
{
    public function login(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->view('account/form', ['register' => false, 'error' => null]);
            return;
        }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->verifyToken()) {
                return;
            }
            $email = $this->field('email');
            $password = $this->field('password', false);
            $db = Database::connection();
            $query = $db->prepare('SELECT id, role, password_hash, failed_logins, locked_until FROM users WHERE email = ?');
            $query->execute([$email]);
            $user = $query->fetch();
            if ($user && ($user['locked_until'] === null || strtotime($user['locked_until'] . ' UTC') <= time())) {
                if (password_verify($password, $user['password_hash'])) {
                    $db->prepare('UPDATE users SET failed_logins = 0, locked_until = NULL WHERE id = ?')->execute([$user['id']]);
                    $this->signIn((int) $user['id'], $user['role']);
                    return;
                }
                $db->prepare('UPDATE users SET locked_until = IF(locked_until IS NULL AND failed_logins >= 4, UTC_TIMESTAMP() + INTERVAL 15 MINUTE, NULL), failed_logins = IF(locked_until IS NOT NULL OR failed_logins >= 4, 0, failed_logins + 1) WHERE id = ? AND (locked_until IS NULL OR locked_until <= UTC_TIMESTAMP())')->execute([$user['id']]);
            }
            $error = 'No se pudo iniciar sesión. Revisa tus datos o vuelve a intentar más tarde.';
        }
        $this->view('account/form', ['register' => false, 'error' => $error]);
    }

    public function register(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/cuenta/ingresar');
            return;
        }
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->verifyToken()) {
                return;
            }
            $name = $this->field('name');
            $email = $this->field('email');
            $password = $this->field('password', false);
            if ($name === '' || strlen($name) > 160 || strlen($email) > 190 ||
                !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 ||
                strlen($password) > 72 || str_contains($password, "\0")) {
                $error = 'Revisa tu nombre y correo. Usa una contraseña de entre 8 y 72 bytes.';
            } else {
                $db = Database::connection();
                try {
                    $db->prepare('INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)')
                        ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
                    $this->signIn((int) $db->lastInsertId(), 'guest');
                    return;
                } catch (PDOException $exception) {
                    if (($exception->errorInfo[1] ?? null) !== 1062) {
                        throw $exception;
                    }
                    $error = 'Ya existe una cuenta con ese correo.';
                }
            }
        }
        $this->view('account/form', ['register' => true, 'error' => $error]);
    }

    public function logout(): void
    {
        if ($this->verifyToken()) {
            $_SESSION = [];
            session_regenerate_id(true);
            $this->redirect('/');
        }
    }

    private function field(string $name, bool $trim = true): string
    {
        $value = $_POST[$name] ?? '';
        return is_string($value) ? ($trim ? trim($value) : $value) : '';
    }

    private function signIn(int $id, string $role): void
    {
        session_regenerate_id(true);
        unset($_SESSION['token']);
        $_SESSION['user_id'] = $id;
        $_SESSION['role'] = $role;
        $this->redirect($role === 'admin' ? '/admin/alojamientos' : '/');
    }
}
