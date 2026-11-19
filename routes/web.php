<?php
declare(strict_types=1);

use App\Controllers\AccommodationController;
use App\Controllers\ActivityController;
use App\Controllers\HomeController;
use App\Controllers\AccountController;
use App\Controllers\AdminController;
use App\Controllers\AdminActivityController;
use App\Controllers\ReservationController;
use App\Controllers\Admin\EventsController;
use App\Controllers\Admin\PaymentsController;
use App\Controllers\Admin\ReservationsController;
use App\Controllers\Admin\TransportsController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/alojamientos', [AccommodationController::class, 'index']);
    $router->get('/alojamientos/detalle', [AccommodationController::class, 'detail']);
    $router->get('/alojamientos/reservar', [ReservationController::class, 'form']);
    $router->post('/alojamientos/reservar', [ReservationController::class, 'create']);
    $router->get('/reservas', [ReservationController::class, 'status']);
    $router->post('/reservas/comprobante', [ReservationController::class, 'upload']);
    $router->get('/mis-reservas', [ReservationController::class, 'mine']);
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

    $router->get('/admin/reservaciones', [ReservationsController::class, 'index']);
    $router->get('/admin/reservaciones/detalle', [ReservationsController::class, 'detail']);
    $router->post('/admin/reservaciones/confirmar', [ReservationsController::class, 'confirm']);
    $router->post('/admin/reservaciones/cancelar', [ReservationsController::class, 'cancel']);

    $router->get('/admin/pagos', [PaymentsController::class, 'index']);
    $router->post('/admin/pagos/aprobar', [PaymentsController::class, 'approve']);
    $router->post('/admin/pagos/rechazar', [PaymentsController::class, 'reject']);
    $router->get('/admin/pagos/comprobante', [PaymentsController::class, 'receipt']);

    $router->get('/admin/eventos', [EventsController::class, 'index']);
    $router->get('/admin/eventos/crear', [EventsController::class, 'form']);
    $router->post('/admin/eventos/crear', [EventsController::class, 'save']);
    $router->post('/admin/eventos/eliminar', [EventsController::class, 'delete']);

    $router->get('/admin/transportes', [TransportsController::class, 'index']);
    $router->get('/admin/transportes/crear', [TransportsController::class, 'form']);
    $router->post('/admin/transportes/crear', [TransportsController::class, 'save']);
    $router->post('/admin/transportes/eliminar', [TransportsController::class, 'delete']);
};
