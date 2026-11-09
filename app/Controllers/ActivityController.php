<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Activity;
use DateTimeImmutable;

final class ActivityController extends Controller
{
    public function index(): void
    {
        $model = new Activity();
        $groups = array_fill_keys(array_keys(Activity::SECTIONS), []);
        foreach ($model->published() as $activity) {
            $groups[$activity['catalog_section']][] = $activity;
        }
        $this->view('activities/index', [
            'groups' => $groups,
            'sections' => Activity::SECTIONS,
            'requests' => isset($_SESSION['user_id']) ? $model->requestsFor((int) $_SESSION['user_id']) : [],
            'notice' => $_SESSION['activity_notice'] ?? null,
        ]);
        unset($_SESSION['activity_notice']);
    }

    public function request(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/cuenta/ingresar');
            return;
        }
        if (!$this->verifyToken()) {
            return;
        }

        $id = filter_input(INPUT_POST, 'activity_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $participants = filter_input(INPUT_POST, 'participants', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
        $date = $_POST['date'] ?? '';
        $parsed = is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date)
            ? DateTimeImmutable::createFromFormat('!Y-m-d', $date)
            : false;
        if (!$id || !$participants || !$parsed || $parsed->format('Y-m-d') !== $date || $date < date('Y-m-d')) {
            $_SESSION['activity_notice'] = 'Selecciona una fecha de hoy en adelante y un número válido de personas.';
        } else {
            $_SESSION['activity_notice'] = (new Activity())->request($id, (int) $_SESSION['user_id'], $date, $participants);
        }
        $this->redirect('/actividades#mis-solicitudes');
    }
}
