<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transporte | Panel Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<main class="catalog section-wrap admin-panel">
    <h1>Logística de transporte</h1>
    <?php require __DIR__ . '/_nav.php'; ?>
    <?php if ($notice): ?><p class="admin-notice" role="status"><?= $e($notice) ?></p><?php endif; ?>
    <p class="catalog-lead">Asigna vehículos, choferes y fechas de recogida o regreso a cada reservación.</p>

    <div class="admin-actions">
        <a class="button button-gold" href="<?= $e($app['base_url']) ?>/admin/transportes/crear">Nuevo transporte</a>
    </div>

    <?php if (!$transports): ?>
        <p class="admin-empty">Aún no hay transportes asignados.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Folio</th><th>Huésped</th><th>Residencia</th><th>Motivo</th><th>Vehículo</th><th>Fechas</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($transports as $transport): ?>
                    <tr>
                        <td><a href="<?= $e($app['base_url']) ?>/admin/reservaciones/detalle?id=<?= $e($transport['reservation_id']) ?>"><?= $e($transport['folio'] ?: '#' . $transport['reservation_id']) ?></a></td>
                        <td><?= $e($transport['guest_name']) ?><br><small><?= $e($transport['guest_phone'] ?: 'Sin teléfono') ?></small></td>
                        <td><?= $e($transport['accommodation_name']) ?></td>
                        <td><?= $e($transport['reason']) ?></td>
                        <td><?= $e($transport['vehicle_type']) ?><?= $transport['plates'] ? ' · ' . $e($transport['plates']) : '' ?><br><small><?= $e($transport['driver'] ?: 'Sin chofer asignado') ?></small></td>
                        <td><?= $e($transport['pickup_date'] ?: '—') ?><br><small>Regreso: <?= $e($transport['return_date'] ?: '—') ?></small></td>
                        <td><span class="status-badge status-<?= $e($transport['status']) ?>"><?= $e(\App\Models\Transport::label($transport['status'])) ?></span></td>
                        <td>
                            <div class="admin-actions">
                                <a class="stay-action-link" href="<?= $e($app['base_url']) ?>/admin/transportes/crear?id=<?= $e($transport['id']) ?>">Editar</a>
                                <form method="post" action="<?= $e($app['base_url']) ?>/admin/transportes/eliminar" onsubmit="return confirm('¿Eliminar esta asignación de transporte?');">
                                    <input type="hidden" name="token" value="<?= $e($token) ?>">
                                    <input type="hidden" name="id" value="<?= $e($transport['id']) ?>">
                                    <button class="stay-action-link stay-action-danger" type="submit">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="admin-count"><?= count($transports) ?> transporte(s).</p>
    <?php endif; ?>
</main>
</body>
</html>
