<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Validación de pagos | Panel Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<main class="catalog section-wrap admin-panel">
    <h1>Validación de pagos</h1>
    <?php require __DIR__ . '/_nav.php'; ?>
    <?php if ($notice): ?><p class="admin-notice" role="status"><?= $e($notice) ?></p><?php endif; ?>
    <p class="catalog-lead">Revisa cada comprobante. Al aprobarlo, la residencia queda liberada y la reservación confirmada automáticamente.</p>

    <section class="admin-section">
        <h2>Comprobantes en revisión <span class="admin-pill"><?= count($pending) ?></span></h2>
        <?php if (!$pending): ?>
            <p class="admin-empty">No hay pagos pendientes de revisión.</p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Huésped</th>
                        <th>Residencia</th>
                        <th>Estancia</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Comprobante</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pending as $receipt): ?>
                        <tr>
                            <td><a href="<?= $e($app['base_url']) ?>/admin/reservaciones/detalle?id=<?= $e($receipt['reservation_id']) ?>"><?= $e($receipt['folio']) ?></a></td>
                            <td><?= $e($receipt['guest_name']) ?><br><small><?= $e($receipt['guest_email']) ?><br><?= $e($receipt['guest_phone'] ?: 'Sin teléfono') ?></small></td>
                            <td><?= $e($receipt['accommodation_name']) ?></td>
                            <td><?= $e($receipt['check_in']) ?> → <?= $e($receipt['check_out']) ?></td>
                            <td>$<?= number_format((float) $receipt['amount_reported'], 2) ?><br><small>Total: $<?= number_format((float) $receipt['total_amount'], 2) ?></small></td>
                            <td><?= $e(\App\Models\Reservation::PAYMENT_METHODS[$receipt['payment_method']] ?? $receipt['payment_method']) ?></td>
                            <td>
                                <a class="button button-dark" href="<?= $e($app['base_url']) ?>/admin/pagos/comprobante?id=<?= $e($receipt['id']) ?>" target="_blank" rel="noopener">Ver archivo</a>
                                <small><?= $e($receipt['original_name'] ?: 'Comprobante') ?></small>
                            </td>
                            <td>
                                <div class="admin-actions">
                                    <form method="post" action="<?= $e($app['base_url']) ?>/admin/pagos/aprobar">
                                        <input type="hidden" name="token" value="<?= $e($token) ?>">
                                        <input type="hidden" name="id" value="<?= $e($receipt['id']) ?>">
                                        <button class="button button-gold" type="submit">Aprobar y liberar</button>
                                    </form>
                                    <form method="post" action="<?= $e($app['base_url']) ?>/admin/pagos/rechazar" onsubmit="return confirm('¿Rechazar este comprobante? El huésped podrá subir otro.');">
                                        <input type="hidden" name="token" value="<?= $e($token) ?>">
                                        <input type="hidden" name="id" value="<?= $e($receipt['id']) ?>">
                                        <button class="stay-action-link stay-action-danger" type="submit">Rechazar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="admin-section">
        <h2>Historial reciente</h2>
        <?php if (!$history): ?>
            <p class="admin-empty">Todavía no se han revisado pagos.</p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Folio</th><th>Huésped</th><th>Residencia</th><th>Resultado</th><th>Revisó</th><th>Fecha</th></tr></thead>
                    <tbody>
                    <?php foreach ($history as $receipt): ?>
                        <tr>
                            <td><?= $e($receipt['folio']) ?></td>
                            <td><?= $e($receipt['guest_name']) ?></td>
                            <td><?= $e($receipt['accommodation_name']) ?></td>
                            <td><span class="status-badge status-<?= $e($receipt['review_status']) ?>"><?= $e(\App\Models\Payment::label($receipt['review_status'])) ?></span></td>
                            <td><?= $e($receipt['reviewer_name'] ?: 'Administrador') ?></td>
                            <td><?= $e($receipt['reviewed_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
