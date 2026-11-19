<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservación | Panel Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<main class="catalog section-wrap admin-panel">
    <a class="account-link" href="<?= $e($app['base_url']) ?>/admin/reservaciones">← Volver a reservaciones</a>
    <h1><?= $reservation ? 'Reservación ' . $e($reservation['folio'] ?: '#' . $reservation['id']) : 'Reservación no encontrada' ?></h1>
    <?php require __DIR__ . '/_nav.php'; ?>
    <?php if ($notice): ?><p class="admin-notice" role="status"><?= $e($notice) ?></p><?php endif; ?>

    <?php if ($reservation): ?>
        <div class="admin-grid">
            <section class="admin-card">
                <h2>Estancia</h2>
                <dl class="admin-data">
                    <div><dt>Residencia</dt><dd><?= $e($reservation['accommodation_name']) ?></dd></div>
                    <div><dt>Dirección</dt><dd><?= $e($reservation['neighborhood']) ?></dd></div>
                    <div><dt>Entrada</dt><dd><?= $e($reservation['check_in']) ?></dd></div>
                    <div><dt>Salida</dt><dd><?= $e($reservation['check_out']) ?></dd></div>
                    <div><dt>Huéspedes</dt><dd><?= $e($reservation['guest_count']) ?></dd></div>
                    <div><dt>Estado</dt><dd><span class="status-badge status-<?= $e($reservation['status']) ?>"><?= $e(\App\Models\Reservation::label($reservation['status'])) ?></span></dd></div>
                    <div><dt>Creada</dt><dd><?= $e($reservation['created_at']) ?></dd></div>
                    <div><dt>Apartado vence</dt><dd><?= $e($reservation['hold_expires_at'] ?? '—') ?></dd></div>
                </dl>
                <div class="admin-actions">
                    <?php if (in_array($reservation['status'], ['pending_payment', 'payment_submitted'], true)): ?>
                        <form method="post" action="<?= $e($app['base_url']) ?>/admin/reservaciones/confirmar">
                            <input type="hidden" name="token" value="<?= $e($token) ?>">
                            <input type="hidden" name="id" value="<?= $e($reservation['id']) ?>">
                            <button class="button button-gold" type="submit">Confirmar y liberar residencia</button>
                        </form>
                    <?php endif; ?>
                    <?php if (\App\Models\Reservation::isActive($reservation['status']) || $reservation['status'] === 'rejected'): ?>
                        <form method="post" action="<?= $e($app['base_url']) ?>/admin/reservaciones/cancelar" onsubmit="return confirm('¿Cancelar esta reservación?');">
                            <input type="hidden" name="token" value="<?= $e($token) ?>">
                            <input type="hidden" name="id" value="<?= $e($reservation['id']) ?>">
                            <button class="stay-action-link stay-action-danger" type="submit">Cancelar reservación</button>
                        </form>
                    <?php endif; ?>
                </div>
            </section>

            <section class="admin-card">
                <h2>Huésped</h2>
                <dl class="admin-data">
                    <div><dt>Nombre</dt><dd><?= $e($reservation['guest_name']) ?></dd></div>
                    <div><dt>Correo</dt><dd><a href="mailto:<?= $e($reservation['guest_email']) ?>"><?= $e($reservation['guest_email']) ?></a></dd></div>
                    <div><dt>Teléfono</dt><dd><?= $e($reservation['guest_phone'] ?: '—') ?></dd></div>
                    <div><dt>Cuenta</dt><dd><?= $e($reservation['user_name'] ?: 'Reserva sin cuenta') ?></dd></div>
                    <div><dt>Notas</dt><dd><?= nl2br($e($reservation['notes'] ?: 'Sin notas')) ?></dd></div>
                </dl>
            </section>

            <section class="admin-card">
                <h2>Importes</h2>
                <dl class="admin-data">
                    <div><dt>Subtotal noches</dt><dd>$<?= number_format((float) $reservation['nightly_subtotal'], 2) ?></dd></div>
                    <div><dt>Limpieza</dt><dd>$<?= number_format((float) $reservation['cleaning_fee'], 2) ?></dd></div>
                    <div><dt>Impuestos</dt><dd>$<?= number_format((float) $reservation['tax_amount'], 2) ?></dd></div>
                    <div><dt>Total</dt><dd><strong>$<?= number_format((float) $reservation['total_amount'], 2) ?> MXN</strong></dd></div>
                </dl>
            </section>
        </div>

        <section class="admin-section">
            <h2>Comprobantes de pago</h2>
            <?php if (!$reservation['receipts']): ?>
                <p class="admin-empty">El huésped aún no ha subido comprobantes.</p>
            <?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead><tr><th>Fecha</th><th>Método</th><th>Monto</th><th>Estado</th><th>Archivo</th></tr></thead>
                        <tbody>
                        <?php foreach ($reservation['receipts'] as $receipt): ?>
                            <tr>
                                <td><?= $e($receipt['created_at']) ?></td>
                                <td><?= $e(\App\Models\Reservation::PAYMENT_METHODS[$receipt['payment_method']] ?? $receipt['payment_method']) ?></td>
                                <td>$<?= number_format((float) $receipt['amount_reported'], 2) ?></td>
                                <td><span class="status-badge status-<?= $e($receipt['review_status']) ?>"><?= $e(\App\Models\Payment::label($receipt['review_status'])) ?></span></td>
                                <td><a class="stay-action-link" href="<?= $e($app['base_url']) ?>/admin/pagos/comprobante?id=<?= $e($receipt['id']) ?>" target="_blank" rel="noopener">Abrir</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p><a class="button button-dark" href="<?= $e($app['base_url']) ?>/admin/pagos">Revisar pagos pendientes</a></p>
            <?php endif; ?>
        </section>

        <section class="admin-section">
            <h2>Eventos vinculados</h2>
            <?php if (!$reservation['events']): ?><p class="admin-empty">Sin eventos registrados.</p><?php endif; ?>
            <?php foreach ($reservation['events'] as $event): ?>
                <article class="admin-row">
                    <strong><?= $e($event['name']) ?></strong>
                    <span><?= $e(\App\Models\Event::typeLabel($event['event_type'])) ?> · <?= $e($event['event_date']) ?> · <?= $e(\App\Models\Event::statusLabel($event['status'])) ?></span>
                    <a class="stay-action-link" href="<?= $e($app['base_url']) ?>/admin/eventos/crear?id=<?= $e($event['id']) ?>">Editar evento</a>
                </article>
            <?php endforeach; ?>
            <p><a class="button button-gold" href="<?= $e($app['base_url']) ?>/admin/eventos/crear?reservacion=<?= $e($reservation['id']) ?>">Agregar evento</a></p>
        </section>

        <section class="admin-section">
            <h2>Transporte asignado</h2>
            <?php if (!$reservation['transports']): ?><p class="admin-empty">Sin transporte registrado.</p><?php endif; ?>
            <?php foreach ($reservation['transports'] as $transport): ?>
                <article class="admin-row">
                    <strong><?= $e($transport['reason']) ?></strong>
                    <span><?= $e($transport['vehicle_type']) ?><?= $transport['driver'] ? ' · ' . $e($transport['driver']) : '' ?> · <?= $e($transport['pickup_date'] ?: 'Sin fecha') ?> · <?= $e(\App\Models\Transport::label($transport['status'])) ?></span>
                    <a class="stay-action-link" href="<?= $e($app['base_url']) ?>/admin/transportes/crear?id=<?= $e($transport['id']) ?>">Editar transporte</a>
                </article>
            <?php endforeach; ?>
            <p><a class="button button-gold" href="<?= $e($app['base_url']) ?>/admin/transportes/crear?reservacion=<?= $e($reservation['id']) ?>">Asignar transporte</a></p>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
