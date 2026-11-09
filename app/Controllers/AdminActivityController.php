<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Activity;

final class AdminActivityController extends Controller
{
    private const CATEGORIES = ['naturaleza', 'cultura', 'deportes', 'social', 'otro'];

    public function index(): void
    {
        $db = $this->adminDatabase();
        if (!$db) {
            return;
        }
        $activities = $db->query('SELECT * FROM activities ORDER BY name')->fetchAll();
        $requests = $db->query(
            "SELECT r.*, a.name AS activity, u.full_name, u.email
             FROM activity_requests r
             JOIN activities a ON a.id = r.activity_id JOIN users u ON u.id = r.user_id
             ORDER BY r.status <> 'pending', r.requested_date, r.requested_time, r.id"
        )->fetchAll();
        $editId = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
        $this->view('admin/activities', [
            'activities' => $activities,
            'requests' => $requests,
            'now' => date('Y-m-d H:i:s'),
            'edit' => array_column($activities, null, 'id')[$editId] ?? [],
            'categories' => self::CATEGORIES,
            'sections' => Activity::SECTIONS,
            'notice' => $_SESSION['admin_activity_notice'] ?? null,
        ]);
        unset($_SESSION['admin_activity_notice']);
    }

    public function save(): void
    {
        $db = $this->adminDatabase();
        if (!$db || !$this->verifyToken()) {
            return;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = $this->field('name');
        $category = $this->field('category');
        $section = $this->field('catalog_section');
        $description = $this->field('description');
        $price = $this->field('price');
        $duration = $this->optionalCount('duration');
        $capacity = $this->optionalCount('capacity');
        if (!is_int($id) || $id < 0 || $name === '' || mb_strlen($name) > 160 ||
            !in_array($category, self::CATEGORIES, true) || !isset(Activity::SECTIONS[$section]) || strlen($description) > 65535 ||
            ($price !== '' && !preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', $price)) ||
            $duration === false || $capacity === false) {
            $_SESSION['admin_activity_notice'] = 'Revisa los datos de la actividad.';
            $this->redirect('/admin/actividades' . ($id > 0 ? '?editar=' . $id : ''));
            return;
        }
        $values = [$name, $category, $section, $description, $price === '' ? '0' : $price, $duration, $capacity, isset($_POST['is_published']) ? 1 : 0];
        $columns = 'name = ?, category = ?, catalog_section = ?, description = ?, price = ?, duration_minutes = ?, capacity = ?, is_published = ?';
        if ($id) {
            $db->prepare("UPDATE activities SET $columns WHERE id = ?")->execute([...$values, $id]);
        } else {
            $db->prepare("INSERT INTO activities SET $columns")->execute($values);
        }
        $_SESSION['admin_activity_notice'] = 'Actividad guardada.';
        $this->redirect('/admin/actividades');
    }

    public function review(): void
    {
        $db = $this->adminDatabase();
        if (!$db || !$this->verifyToken()) {
            return;
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $decision = $this->field('decision');
        $response = $this->field('response');
        if (!$id || !in_array($decision, ['confirmed', 'declined'], true) || mb_strlen($response) > 500) {
            http_response_code(400);
            echo 'La decisión o el mensaje no son válidos.';
            return;
        }
        $query = $db->prepare(
            "UPDATE activity_requests SET status = ?, response = ?, reviewed_by = ?, reviewed_at = UTC_TIMESTAMP()
             WHERE id = ? AND status = 'pending'
             AND (? = 'declined' OR TIMESTAMP(requested_date, COALESCE(requested_time, '23:59:59')) >= ?)"
        );
        $query->execute([$decision, $response ?: null, $_SESSION['user_id'], $id, $decision, date('Y-m-d H:i:s')]);
        $_SESSION['admin_activity_notice'] = $query->rowCount() === 1
            ? 'Decisión guardada. El cliente puede consultarla en Mis solicitudes.'
            : 'La solicitud ya se revisó, no existe o la fecha solicitada ya pasó.';
        $this->redirect('/admin/actividades#solicitudes');
    }

    private function field(string $name): string
    {
        $value = $_POST[$name] ?? '';
        return is_string($value) ? trim($value) : '';
    }

    private function optionalCount(string $name): int|false|null
    {
        if (($_POST[$name] ?? '') === '') {
            return null;
        }
        return filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
    }
}
