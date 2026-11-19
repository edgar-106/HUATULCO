<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estado de tu reservación | Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<main class="catalog section-wrap stay-detail">
    <a class="account-link" href="<?= $e($app['base_url']) ?>/alojamientos">← Seguir explorando alojamientos</a>
    <?php if (!$reservation): ?>
        <h1>Reservación no encontrada</h1>
        <p>Revisa el folio o <a class="account-link" href="<?= $e($app['base_url']) ?>/alojamientos">vuelve al catálogo</a>.</p>
    <?php else: ?>
        <?php if ($justCreated): ?>
            <p class="admin-notice" role="status">¡Reservación creada! Guarda tu folio <strong><?= $e($reservation['folio']) ?></strong> y sube tu comprobante para liberar la residencia.</p>
        <?php endif; ?>
        <?php if ($uploaded): ?>
            <p class="admin-notice" role="status">Comprobante recibido. El equipo lo revisará y confirmará tu reservación.</p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="account-error" role="alert"><?= $e($error) ?></p>
        <?php endif; ?>

        <h1>Reservación <?= $e($reservation['folio']) ?></h1>
        <p class="catalog-lead">
            <span class="status-badge status-<?= $e($reservation['status']) ?>"><?= $e(\App\Models\Reservation::label($reservation['status'])) ?></span>
            <?php if ($reservation['status'] === 'confirmed'): ?>
                · Tu residencia está liberada. ¡Te esperamos!
            <?php elseif (in_array($reservation['status'], ['pending_payment', 'payment_submitted'], true)): ?>
                · Apartado vigente hasta <?= $e($reservation['hold_expires_at'] ?? 'nuevo aviso') ?>.
            <?php endif; ?>
        </p>

        <div class="admin-grid">
            <section class="admin-card">
                <h2>Tu estancia</h2>
                <dl class="admin-data">
                    <div><dt>Residencia</dt><dd><?= $e($reservation['accommodation_name']) ?></dd></div>
                    <div><dt>Dirección</dt><dd><?= $e($reservation['neighborhood']) ?></dd></div>
                    <div><dt>Entrada</dt><dd><?= $e($reservation['check_in']) ?></dd></div>
                    <div><dt>Salida</dt><dd><?= $e($reservation['check_out']) ?></dd></div>
                    <div><dt>Huéspedes</dt><dd><?= $e($reservation['guest_count']) ?></dd></div>
                    <div><dt>Total</dt><dd><strong>$<?= number_format((float) $reservation['total_amount'], 2) ?> MXN</strong></dd></div>
                </dl>
            </section>
            <section class="admin-card">
                <h2>Titular</h2>
                <dl class="admin-data">
                    <div><dt>Nombre</dt><dd><?= $e($reservation['guest_name']) ?></dd></div>
                    <div><dt>Correo</dt><dd><?= $e($reservation['guest_email']) ?></dd></div>
                    <div><dt>Teléfono</dt><dd><?= $e($reservation['guest_phone'] ?: '—') ?></dd></div>
                    <div><dt>Notas</dt><dd><?= nl2br($e($reservation['notes'] ?: 'Sin notas')) ?></dd></div>
                </dl>
            </section>
        </div>

        <section class="admin-section">
            <h2>Cómo pagar</h2>
            <div class="payment-instructions">
                <p><strong>Transferencia bancaria</strong><br>BBVA · Huatulco Travel · CLABE 0121 8000 1234 5678 90</p>
                <p><strong>Depósito en efectivo</strong><br>Sucursal BBVA a nombre de Huatulco Travel S.A. de C.V.</p>
                <p><strong>OXXO</strong><br>Solicita la referencia por correo a pagos@huatulcotravel.com</p>
                <p>Sube una foto legible o PDF de tu comprobante. El equipo lo valida manualmente antes de liberar la residencia.</p>
            </div>
        </section>

        <?php if (in_array($reservation['status'], ['pending_payment', 'payment_submitted', 'rejected'], true)): ?>
            <section class="admin-section">
                <h2>Subir comprobante</h2>
                <form method="post" action="<?= $e($app['base_url']) ?>/reservas/comprobante" enctype="multipart/form-data" class="travel-form admin-form">
                    <input type="hidden" name="token" value="<?= $e($token) ?>">
                    <input type="hidden" name="folio" value="<?= $e($reservation['folio']) ?>">
                    <div class="admin-fields">
                        <label>Método de pago <select name="payment_method">
                            <?php foreach ($methods as $key => $label): ?>
                                <option value="<?= $e($key) ?>"><?= $e($label) ?></option>
                            <?php endforeach; ?>
                        </select></label>
                        <label>Monto pagado (MXN) <input type="number" name="amount" min="0" step="0.01" value="<?= $e(number_format((float) $reservation['total_amount'], 2, '.', '')) ?>"></label>
                    </div>
                    <label>Archivo <input type="file" name="receipt" accept="image/jpeg,image/png,image/webp,application/pdf" required></label>
                    <small>JPG, PNG, WEBP o PDF. Máximo 5 MB.</small>
                    <button class="button button-gold" type="submit">Enviar comprobante</button>
                </form>
            </section>
        <?php endif; ?>

        <section class="admin-section">
            <h2>Comprobantes enviados</h2>
            <?php if (!$reservation['receipts']): ?>
                <p class="admin-empty">Todavía no has subido comprobantes.</p>
            <?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead><tr><th>Fecha</th><th>Método</th><th>Monto</th><th>Estado</th></tr></thead>
                        <tbody>
                        <?php foreach ($reservation['receipts'] as $receipt): ?>
                            <tr>
                                <td><?= $e($receipt['created_at']) ?></td>
                                <td><?= $e($methods[$receipt['payment_method']] ?? $receipt['payment_method']) ?></td>
                                <td>$<?= number_format((float) $receipt['amount_reported'], 2) ?></td>
                                <td><span class="status-badge status-<?= $e($receipt['review_status']) ?>"><?= $e(\App\Models\Payment::label($receipt['review_status'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
