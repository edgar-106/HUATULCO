<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Accommodation;

final class AccommodationController extends Controller
{
    public function index(): void
    {
        $this->view('accommodations/index', [
            'accommodations' => (new Accommodation())->availableForCatalog(),
            'app' => require dirname(__DIR__, 2) . '/config/app.php',
        ]);
    }
}
