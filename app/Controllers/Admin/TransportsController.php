<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Transport;
use PDO;

final class TransportsController extends Controller
{
    public function index(): void
    {
        $db = $this->adminDatabase();
        if (!$db) {
            return;
        }
        $reservationId = filter_input(INPUT_GET, 'reservacion', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;

        $this->view('admin/transports', [
            'transports' => (new Transport())->all($reservationId),
            'reservationId' => $reservationId,
            'notice' => isset($_GET['ok']) ? 'Asignación de transporte guardada.' : null,
        ]);
    }

    public function form(): void
    {
        $db = $this->adminDatabase();
        if (!$db) {
            return;
        }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $transport = $id ? (new Transport())->find($id) : null;
        if ($id && !$transport) {
            http_response_code(404);
        }
        $values = $transport ?? ['status' => 'assigned'];
        if (!$transport && ($reservationId = filter_input(INPUT_GET, 'reservacion', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]))) {
            $values['reservation_id'] = $reservationId;
        }
        $this->renderForm($db, $transport, null, $values);
    }

    public function save(): void
    {
        $db = $this->adminDatabase();
        if (!$db || !$this->verifyToken()) {
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $values = [
            'reason' => trim((string) ($_POST['reason'] ?? '')),
            'vehicle_type' => trim((string) ($_POST['vehicle_type'] ?? '')),
            'plates' => trim((string) ($_POST['plates'] ?? '')),
            'driver' => trim((string) ($_POST['driver'] ?? '')),
            'pickup_date' => trim((string) ($_POST['pickup_date'] ?? '')),
            'return_date' => trim((string) ($_POST['return_date'] ?? '')),
            'notes' => trim((string) ($_POST['notes'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'assigned'),
            'reservation_id' => $reservationId,
        ];

        $error = null;
        if (
            !$reservationId
            || !isset(Transport::STATUSES[$values['status']])
            || mb_strlen($values['reason']) < 2 || mb_strlen($values['reason']) > 160
            || mb_strlen($values['vehicle_type']) < 2 || mb_strlen($values['vehicle_type']) > 80
            || mb_strlen($values['plates']) > 20
            || mb_strlen($values['driver']) > 160
            || mb_strlen($values['notes']) > 500
            || !$this->validDate($values['pickup_date'])
            || !$this->validDate($values['return_date'])
        ) {
            $error = 'Revisa la reservación, el motivo y las fechas del transporte.';
        }
        if (
            $error === null
            && $values['pickup_date'] !== ''
            && $values['return_date'] !== ''
            && $values['return_date'] < $values['pickup_date']
        ) {
            $error = 'La fecha de devolución debe ser posterior a la de recogida.';
        }

        if ($error === null) {
            $values['id'] = $id;
            (new Transport())->save($values);
            $this->redirect('/admin/transportes?ok=1');
            return;
        }

        $values['id'] = $id;
        $this->renderForm($db, $id > 0 ? ['id' => $id] : null, $error, $values);
    }

    public function delete(): void
    {
        $db = $this->adminDatabase();
        if (!$db || !$this->verifyToken()) {
            return;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id) {
            (new Transport())->delete($id);
        }
        $this->redirect('/admin/transportes?ok=1');
    }

    private function renderForm(PDO $db, ?array $transport, ?string $error, array $values): void
    {
        $this->view('admin/transport-form', [
            'transport' => $transport,
            'values' => $values,
            'error' => $error,
            'reservations' => $db->query(
                'SELECT r.id, r.folio, r.guest_name, r.check_in, r.check_out, a.name AS accommodation_name
                 FROM reservations r JOIN accommodations a ON a.id = r.accommodation_id
                 ORDER BY r.created_at DESC, r.id DESC LIMIT 200'
            )->fetchAll(),
        ]);
    }

    private function validDate(string $value): bool
    {
        return $value === '' || (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
    }
}
