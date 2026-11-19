<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Reservation;

final class ReservationsController extends Controller
{
    public function index(): void
    {
        $db = $this->adminDatabase();
        if (!$db) {
            return;
        }
        $accommodationId = filter_input(INPUT_GET, 'alojamiento', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
        $status = isset($_GET['estado'], Reservation::STATUSES[(string) $_GET['estado']]) ? (string) $_GET['estado'] : null;

        $this->view('admin/reservations', [
            'reservations' => (new Reservation())->all($accommodationId, $status),
            'accommodations' => $db->query('SELECT id, name FROM accommodations WHERE is_active = 1 ORDER BY name')->fetchAll(),
            'accommodationId' => $accommodationId,
            'status' => $status,
            'notice' => isset($_GET['ok']) ? 'Reservación actualizada correctamente.' : null,
        ]);
    }

    public function detail(): void
    {
        $db = $this->adminDatabase();
        if (!$db) {
            return;
        }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $reservation = $id ? (new Reservation())->find($id) : null;
        if (!$reservation) {
            http_response_code(404);
        }
        $this->view('admin/reservation', [
            'reservation' => $reservation,
            'notice' => isset($_GET['ok']) ? 'Reservación actualizada correctamente.' : null,
        ]);
    }

    public function confirm(): void
    {
        $db = $this->adminDatabase();
        if (!$db || !$this->verifyToken()) {
            return;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id) {
            (new Reservation())->confirm($id);
        }
        $this->redirect('/admin/reservaciones/detalle?id=' . (int) $id . '&ok=1');
    }

    public function cancel(): void
    {
        $db = $this->adminDatabase();
        if (!$db || !$this->verifyToken()) {
            return;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id) {
            (new Reservation())->cancel($id);
        }
        $this->redirect('/admin/reservaciones/detalle?id=' . (int) $id . '&ok=1');
    }
}
