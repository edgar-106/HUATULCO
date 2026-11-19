<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Accommodation;

final class HomeController extends Controller
{
    public function index(): void
    {
        $app = require dirname(__DIR__, 2) . '/config/app.php';
        $app['default_locale'] = $_SESSION['locale'] ?? $app['default_locale'];
        $this->view('home/index', [
            'accommodations' => (new Accommodation())->featured(),
            'mapAccommodations' => (new Accommodation())->withCoordinates(),
            'app' => $app,
        ]);
    }
}
