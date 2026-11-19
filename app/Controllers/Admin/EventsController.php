<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Event;
use PDO;

final class EventsController extends Controller
{
    public function index(): void
    {
        $db = $this->adminDatabase();
        if (!$db) {
            return;
        }
        $accommodationId = filter_input(INPUT_GET, 'alojamiento', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
        $type = isset($_GET['tipo'], Event::TYPES[(string) $_GET['tipo']]) ? (string) $_GET['tipo'] : null;
        $sporadic = isset($_GET['modalidad']) && $_GET['modalidad'] !== ''
            ? (string) $_GET['modalidad'] === 'esporadico'
            : null;
        $status = isset($_GET['estado'], Event::STATUSES[(string) $_GET['estado']]) ? (string) $_GET['estado'] : null;

        $this->view('admin/events', [
            'events' => (new Event())->all($accommodationId, $type, $sporadic, $status),
            'accommodations' => $db->query('SELECT id, name FROM accommodations WHERE is_active = 1 ORDER BY name')->fetchAll(),
            'accommodationId' => $accommodationId,
            'type' => $type,
            'sporadic' => $sporadic,
            'status' => $status,
            'notice' => isset($_GET['ok']) ? 'Evento guardado correctamente.' : null,
        ]);
    }

    public function form(): void
    {
        $db = $this->adminDatabase();
        if (!$db) {
            return;
        }
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $event = $id ? (new Event())->find($id) : null;
        if ($id && !$event) {
            http_response_code(404);
        }
        $values = $event ?? [];
        if (!$event && ($reservationId = filter_input(INPUT_GET, 'reservacion', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]))) {
            $query = $db->prepare('SELECT id, accommodation_id, user_id FROM reservations WHERE id = ?');
            $query->execute([$reservationId]);
            if ($reservation = $query->fetch()) {
                $values = [
                    'reservation_id' => $reservation['id'],
                    'accommodation_id' => $reservation['accommodation_id'],
                    'user_id' => $reservation['user_id'],
                ];
            }
        }
        $this->renderForm($db, $event, null, $values);
    }

    public function save(): void
    {
        $db = $this->adminDatabase();
        if (!$db || !$this->verifyToken()) {
            return;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
        $accommodationId = filter_input(INPUT_POST, 'accommodation_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
        $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;
        $values = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'event_type' => (string) ($_POST['event_type'] ?? ''),
            'event_date' => trim((string) ($_POST['event_date'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'attraction_center' => trim((string) ($_POST['attraction_center'] ?? '')),
            'provider' => trim((string) ($_POST['provider'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'scheduled'),
            'is_sporadic' => isset($_POST['is_sporadic']),
            'accommodation_id' => $accommodationId,
            'user_id' => $userId,
            'reservation_id' => $reservationId,
        ];

        $error = null;
        if (
            !$accommodationId
            || !isset(Event::TYPES[$values['event_type']])
            || !isset(Event::STATUSES[$values['status']])
            || mb_strlen($values['name']) < 2 || mb_strlen($values['name']) > 160
            || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $values['event_date'])
            || mb_strlen($values['description']) > 65535
            || mb_strlen($values['attraction_center']) > 160
            || mb_strlen($values['provider']) > 160
        ) {
            $error = 'Revisa el nombre, tipo, fecha y residencia del evento.';
        }

        if ($error === null) {
            $values['id'] = $id;
            (new Event())->save($values);
            $this->redirect('/admin/eventos?ok=1');
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
            (new Event())->delete($id);
        }
        $this->redirect('/admin/eventos?ok=1');
    }

    private function renderForm(PDO $db, ?array $event, ?string $error, array $values): void
    {
        $this->view('admin/event-form', [
            'event' => $event,
            'values' => $values,
            'error' => $error,
            'accommodations' => $db->query('SELECT id, name FROM accommodations WHERE is_active = 1 ORDER BY name')->fetchAll(),
            'users' => $db->query('SELECT id, full_name, email FROM users ORDER BY full_name')->fetchAll(),
            'reservations' => $db->query(
                'SELECT r.id, r.folio, r.guest_name, a.name AS accommodation_name
                 FROM reservations r JOIN accommodations a ON a.id = r.accommodation_id
                 ORDER BY r.created_at DESC, r.id DESC LIMIT 200'
            )->fetchAll(),
        ]);
    }
}
