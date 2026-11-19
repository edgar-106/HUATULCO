<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis reservas | Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<main class="catalog section-wrap stay-detail">
    <a class="account-link" href="<?= $e($app['base_url']) ?>/alojamientos">← Explorar alojamientos</a>
    <h1>Mis reservas</h1>
    <p class="catalog-lead">Consulta el estado de tus apartados y comprobantes de pago.</p>
    <?php if (!$reservations): ?>
        <p class="admin-empty">Aún no tienes reservaciones. <a class="account-link" href="<?= $e($app['base_url']) ?>/alojamientos">Encuentra tu estancia ideal</a>.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Folio</th><th>Residencia</th><th>Estancia</th><th>Total</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><strong><?= $e($reservation['folio'] ?: '#' . $reservation['id']) ?></strong></td>
                        <td><?= $e($reservation['accommodation_name']) ?><br><small><?= $e($reservation['neighborhood']) ?></small></td>
                        <td><?= $e($reservation['check_in']) ?> → <?= $e($reservation['check_out']) ?><br><small><?= $e($reservation['guest_count']) ?> huésped(es)</small></td>
                        <td>$<?= number_format((float) $reservation['total_amount'], 2) ?></td>
                        <td><span class="status-badge status-<?= $e($reservation['status']) ?>"><?= $e(\App\Models\Reservation::label($reservation['status'])) ?></span></td>
                        <td><a class="stay-action-link" href="<?= $e($app['base_url']) ?>/reservas?folio=<?= $e(rawurlencode($reservation['folio'])) ?>">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
