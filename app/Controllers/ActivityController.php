<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Activity;
use DateTimeImmutable;

final class ActivityController extends Controller
{
    private function firstRequestDate(): string
    {
        return (new DateTimeImmutable('today'))->modify('+2 days')->format('Y-m-d');
    }

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
            'firstRequestDate' => $this->firstRequestDate(),
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
        $countOptions = ['options' => ['min_range' => 0, 'max_range' => 65535]];
        $adults = filter_input(INPUT_POST, 'adults', FILTER_VALIDATE_INT, $countOptions);
        $children = filter_input(INPUT_POST, 'children', FILTER_VALIDATE_INT, $countOptions);
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $parsed = is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date)
            ? DateTimeImmutable::createFromFormat('!Y-m-d', $date)
            : false;
        if (!$id || !$parsed || $parsed->format('Y-m-d') !== $date || $date < $this->firstRequestDate() ||
            !is_string($time) || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $time) ||
            !is_int($adults) || !is_int($children) || $adults + $children < 1 || $adults + $children > 65535) {
            $_SESSION['activity_notice'] = 'Elige una fecha desde pasado mañana, una hora válida y al menos un adulto o niño.';
        } else {
            $_SESSION['activity_notice'] = (new Activity())->request($id, (int) $_SESSION['user_id'], $date, $time, $adults, $children);
        }
        $this->redirect('/actividades#mis-solicitudes');
    }
}
