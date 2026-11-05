<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Activity;

final class ActivityController extends Controller
{
    public function index(): void
    {
        $this->view('activities/index', [
            'activities' => (new Activity())->published(),
            'app' => require dirname(__DIR__, 2) . '/config/app.php',
        ]);
    }
}
