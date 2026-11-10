<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Accommodation;

final class AccommodationController extends Controller
{
    public function detail(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $stay = $id ? (new Accommodation())->publishedDetail($id) : null;
        if (!$stay) {
            http_response_code(404);
        }
        $this->view('accommodations/detail', ['stay' => $stay]);
    }

    public function index(): void
    {
        $this->view('accommodations/index', [
            'accommodations' => (new Accommodation())->availableForCatalog(),
            'app' => require dirname(__DIR__, 2) . '/config/app.php',
        ]);
    }
}
