<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Payment;

final class PaymentsController extends Controller
{
    public function index(): void
    {
        $db = $this->adminDatabase();
        if (!$db) {
            return;
        }
        $model = new Payment();
        $notice = null;
        if (isset($_GET['ok'])) {
            $notice = match ((string) $_GET['ok']) {
                'aprobado' => 'Pago aprobado: la residencia quedó liberada para el cliente.',
                'rechazado' => 'Comprobante rechazado. El cliente puede subir uno nuevo.',
                default => 'Pago actualizado.',
            };
        }
        $this->view('admin/payments', [
            'pending' => $model->pending(),
            'history' => $model->history(),
            'notice' => $notice,
        ]);
    }

    public function approve(): void
    {
        $db = $this->adminDatabase();
        if (!$db || !$this->verifyToken()) {
            return;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id) {
            (new Payment())->approve($id, (int) $_SESSION['user_id']);
        }
        $this->redirect('/admin/pagos?ok=aprobado');
    }

    public function reject(): void
    {
        $db = $this->adminDatabase();
        if (!$db || !$this->verifyToken()) {
            return;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id) {
            (new Payment())->reject($id, (int) $_SESSION['user_id']);
        }
        $this->redirect('/admin/pagos?ok=rechazado');
    }

    public function receipt(): void
    {
        $db = $this->adminDatabase();
        if (!$db) {
            return;
        }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $receipt = $id ? (new Payment())->find($id) : null;
        if (!$receipt) {
            http_response_code(404);
            echo 'Comprobante no encontrado.';
            return;
        }

        $root = dirname(__DIR__, 3) . '/storage';
        $full = realpath($root . '/' . ltrim((string) $receipt['receipt_path'], '/'));
        $base = realpath($root);
        if (!$full || !$base || !str_starts_with($full, $base . DIRECTORY_SEPARATOR) || !is_file($full)) {
            http_response_code(404);
            echo 'Archivo no disponible.';
            return;
        }

        $mime = (string) ($receipt['mime_type'] ?: (new \finfo(FILEINFO_MIME_TYPE))->file($full));
        $original = (string) ($receipt['original_name'] ?: basename($full));
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($full));
        header('Content-Disposition: inline; filename="' . rawurlencode($original) . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($full);
    }
}
