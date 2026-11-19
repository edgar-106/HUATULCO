<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservaciones | Panel Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<main class="catalog section-wrap admin-panel">
    <h1>Reservaciones</h1>
    <?php require __DIR__ . '/_nav.php'; ?>
    <?php if ($notice): ?><p class="admin-notice" role="status"><?= $e($notice) ?></p><?php endif; ?>

    <form method="get" action="<?= $e($app['base_url']) ?>/admin/reservaciones" class="admin-filters">
        <label>Residencia
            <select name="alojamiento">
                <option value="">Todas</option>
                <?php foreach ($accommodations as $accommodation): ?>
                    <option value="<?= $e($accommodation['id']) ?>" <?= (int) $accommodationId === (int) $accommodation['id'] ? 'selected' : '' ?>><?= $e($accommodation['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Estado
            <select name="estado">
                <option value="">Todos</option>
                <?php foreach (\App\Models\Reservation::STATUSES as $key => $label): ?>
                    <option value="<?= $e($key) ?>" <?= $status === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button button-dark" type="submit">Filtrar</button>
        <a class="stay-action-link" href="<?= $e($app['base_url']) ?>/admin/reservaciones">Limpiar filtros</a>
    </form>

    <?php if (!$reservations): ?>
        <p class="admin-empty">Aún no hay reservaciones con estos criterios.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>Folio</th>
                    <th>Residencia</th>
                    <th>Huésped</th>
                    <th>Estancia</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><strong><?= $e($reservation['folio'] ?: '#' . $reservation['id']) ?></strong></td>
                        <td><?= $e($reservation['accommodation_name']) ?><br><small><?= $e($reservation['neighborhood']) ?></small></td>
                        <td><?= $e($reservation['guest_name']) ?><br><small><?= $e($reservation['guest_email']) ?></small></td>
                        <td><?= $e($reservation['check_in']) ?> → <?= $e($reservation['check_out']) ?><br><small><?= $e($reservation['guest_count']) ?> huésped(es)</small></td>
                        <td>$<?= number_format((float) $reservation['total_amount'], 2) ?></td>
                        <td>
                            <span class="status-badge status-<?= $e($reservation['status']) ?>"><?= $e(\App\Models\Reservation::label($reservation['status'])) ?></span>
                            <?php if ((int) $reservation['pending_receipts'] > 0): ?>
                                <br><small class="status-note"><?= $e($reservation['pending_receipts']) ?> comprobante(s) por revisar</small>
                            <?php endif; ?>
                        </td>
                        <td><a class="stay-action-link" href="<?= $e($app['base_url']) ?>/admin/reservaciones/detalle?id=<?= $e($reservation['id']) ?>">Ver detalle</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="admin-count"><?= count($reservations) ?> reservación(es).</p>
    <?php endif; ?>
</main>
</body>
</html>
