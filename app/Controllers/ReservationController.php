<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Accommodation;
use App\Models\Payment;
use App\Models\Reservation;
use DateTimeImmutable;
use RuntimeException;

final class ReservationController extends Controller
{
    public function form(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $stay = $id ? (new Accommodation())->publishedDetail($id) : null;
        if (!$stay) {
            http_response_code(404);
        }
        $this->view('reservations/form', [
            'stay' => $stay,
            'values' => [
                'guest_name' => '',
                'guest_email' => '',
                'guest_phone' => '',
                'check_in' => (new DateTimeImmutable('today'))->modify('+7 days')->format('Y-m-d'),
                'check_out' => (new DateTimeImmutable('today'))->modify('+10 days')->format('Y-m-d'),
                'guest_count' => 2,
                'notes' => '',
            ],
            'error' => null,
        ]);
    }

    public function create(): void
    {
        if (!$this->verifyToken()) {
            return;
        }

        $id = filter_input(INPUT_POST, 'accommodation_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $stay = $id ? (new Accommodation())->publishedDetail($id) : null;
        $values = [
            'guest_name' => $this->field('guest_name'),
            'guest_email' => $this->field('guest_email'),
            'guest_phone' => $this->field('guest_phone'),
            'check_in' => $this->field('check_in'),
            'check_out' => $this->field('check_out'),
            'guest_count' => (int) (filter_input(INPUT_POST, 'guest_count', FILTER_VALIDATE_INT) ?: 0),
            'notes' => $this->field('notes'),
        ];

        $error = null;
        if (!$stay) {
            $error = 'El alojamiento no está disponible para reservar.';
        } elseif (
            mb_strlen($values['guest_name']) < 2 || mb_strlen($values['guest_name']) > 160
            || !filter_var($values['guest_email'], FILTER_VALIDATE_EMAIL)
            || !preg_match('/^[0-9+()\-\s]{7,30}$/', $values['guest_phone'])
            || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $values['check_in'])
            || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $values['check_out'])
            || $values['guest_count'] < 1
            || mb_strlen($values['notes']) > 500
        ) {
            $error = 'Revisa tu nombre, correo, teléfono y fechas.';
        }

        if ($error === null) {
            try {
                $created = (new Reservation())->create(
                    (int) $id,
                    isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null,
                    $values['guest_name'],
                    $values['guest_email'],
                    $values['guest_phone'],
                    $values['guest_count'],
                    $values['check_in'],
                    $values['check_out'],
                    $values['notes']
                );
                $this->redirect('/reservas?folio=' . rawurlencode($created['folio']) . '&nueva=1');
                return;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        }

        $this->view('reservations/form', ['stay' => $stay, 'values' => $values, 'error' => $error]);
    }

    public function status(): void
    {
        $folio = strtoupper(trim((string) ($_GET['folio'] ?? '')));
        $reservation = $folio !== '' ? (new Reservation())->findByFolio($folio) : null;
        if (!$reservation) {
            http_response_code(404);
        }
        $this->view('reservations/status', [
            'reservation' => $reservation,
            'methods' => Reservation::PAYMENT_METHODS,
            'justCreated' => isset($_GET['nueva']),
            'uploaded' => isset($_GET['subido']),
            'error' => isset($_GET['error']) ? (string) $_GET['error'] : null,
        ]);
    }

    public function upload(): void
    {
        if (!$this->verifyToken()) {
            return;
        }
        $folio = strtoupper(trim((string) ($_POST['folio'] ?? '')));
        $reservation = $folio !== '' ? (new Reservation())->findByFolio($folio) : null;
        if (!$reservation) {
            http_response_code(404);
            return;
        }

        $error = $this->storeReceipt($reservation);
        $target = '/reservas?folio=' . rawurlencode($folio);
        if ($error !== null) {
            $this->redirect($target . '&error=' . rawurlencode($error));
            return;
        }
        $this->redirect($target . '&subido=1');
    }

    public function mine(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/cuenta/ingresar');
            return;
        }
        $this->view('reservations/mine', [
            'reservations' => (new Reservation())->forUser((int) $_SESSION['user_id']),
        ]);
    }

    private function storeReceipt(array $reservation): ?string
    {
        $file = $_FILES['receipt'] ?? null;
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return 'Adjunta una foto o PDF de tu comprobante de pago.';
        }
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return 'No se pudo recibir el archivo. Intenta de nuevo.';
        }
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            return 'El comprobante debe pesar como máximo 5 MB.';
        }

        $mimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
        ];
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset($mimeTypes[$mime])) {
            return 'Sube una imagen JPG, PNG, WEBP o un archivo PDF.';
        }

        $method = (string) ($_POST['payment_method'] ?? 'transfer');
        if (!isset(Reservation::PAYMENT_METHODS[$method])) {
            $method = 'other';
        }
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);

        $relative = 'receipts/' . date('Y') . '/' . date('m');
        $directory = dirname(__DIR__, 2) . '/storage/' . $relative;
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            return 'No se pudo preparar el almacenamiento del comprobante.';
        }

        $name = bin2hex(random_bytes(16)) . '.' . $mimeTypes[$mime];
        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $name)) {
            return 'No se pudo guardar el comprobante.';
        }

        try {
            (new Payment())->create(
                (int) $reservation['id'],
                $method,
                $amount === false ? null : (float) $amount,
                $relative . '/' . $name,
                (string) ($file['name'] ?? $name),
                $mime,
                (int) ($file['size'] ?? 0)
            );
        } catch (RuntimeException $exception) {
            @unlink($directory . '/' . $name);
            return $exception->getMessage();
        }
        return null;
    }

    private function field(string $name): string
    {
        $value = $_POST[$name] ?? '';
        return is_string($value) ? trim($value) : '';
    }
}
