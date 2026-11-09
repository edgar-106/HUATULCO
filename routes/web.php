<?php
declare(strict_types=1);

use App\Controllers\AccommodationController;
use App\Controllers\ActivityController;
use App\Controllers\HomeController;
use App\Controllers\AccountController;
use App\Controllers\AdminController;
use App\Controllers\AdminActivityController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/alojamientos', [AccommodationController::class, 'index']);
    $router->get('/actividades', [ActivityController::class, 'index']);
    $router->post('/actividades/solicitar', [ActivityController::class, 'request']);
    $router->get('/admin/actividades', [AdminActivityController::class, 'index']);
    $router->post('/admin/actividades', [AdminActivityController::class, 'save']);
    $router->post('/admin/actividades/revisar', [AdminActivityController::class, 'review']);
    $router->get('/cuenta/ingresar', [AccountController::class, 'login']);
    $router->post('/cuenta/ingresar', [AccountController::class, 'login']);
    $router->get('/cuenta/registro', [AccountController::class, 'register']);
    $router->post('/cuenta/registro', [AccountController::class, 'register']);
    $router->post('/cuenta/salir', [AccountController::class, 'logout']);
    $router->get('/admin/alojamientos', [AdminController::class, 'index']);
    $router->post('/admin/alojamientos', [AdminController::class, 'save']);
};
