<?php
declare(strict_types=1);

use App\Controllers\AccommodationController;
use App\Controllers\ActivityController;
use App\Controllers\HomeController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/alojamientos', [AccommodationController::class, 'index']);
    $router->get('/actividades', [ActivityController::class, 'index']);
};
