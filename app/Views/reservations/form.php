<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservar <?= $e($stay['name'] ?? 'alojamiento') ?> | Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<main class="catalog section-wrap stay-detail">
    <a class="account-link" href="<?= $e($app['base_url']) ?>/alojamientos/detalle?id=<?= $e($stay['id'] ?? '') ?>">← Volver al alojamiento</a>
    <?php if (!$stay): ?>
        <h1>Alojamiento no disponible</h1>
        <p>El alojamiento que buscas no existe o ya no está publicado. <a class="account-link" href="<?= $e($app['base_url']) ?>/alojamientos">Ver catálogo</a></p>
    <?php else: ?>
        <h1>Reserva tu estancia</h1>
        <p class="catalog-lead"><?= $e($stay['name']) ?> · <?= $e($stay['neighborhood']) ?></p>
        <?php if ($error): ?><p class="account-error" role="alert"><?= $e($error) ?></p><?php endif; ?>

        <div class="admin-grid reservation-layout">
            <form id="reservation-form" method="post" action="<?= $e($app['base_url']) ?>/alojamientos/reservar" class="travel-form"
                  data-nightly="<?= $e($stay['nightly_rate']) ?>" data-cleaning="<?= $e($stay['cleaning_fee']) ?>">
                <input type="hidden" name="token" value="<?= $e($token) ?>">
                <input type="hidden" name="accommodation_id" value="<?= $e($stay['id']) ?>">

                <fieldset>
                    <legend>Datos del huésped</legend>
                    <label>Nombre completo <input name="guest_name" maxlength="160" autocomplete="name" required value="<?= $e($values['guest_name']) ?>"></label>
                    <label>Correo electrónico <input type="email" name="guest_email" maxlength="190" autocomplete="email" required value="<?= $e($values['guest_email']) ?>"></label>
                    <label>Teléfono <input name="guest_phone" maxlength="30" autocomplete="tel" required value="<?= $e($values['guest_phone']) ?>" placeholder="+52 958 000 0000"></label>
                </fieldset>

                <fieldset>
                    <legend>Fechas y huéspedes</legend>
                    <div class="admin-fields">
                        <label>Entrada <input type="date" name="check_in" id="check_in" required value="<?= $e($values['check_in']) ?>"></label>
                        <label>Salida <input type="date" name="check_out" id="check_out" required value="<?= $e($values['check_out']) ?>"></label>
                        <label>Huéspedes <input type="number" name="guest_count" min="1" max="<?= $e($stay['capacity']) ?>" required value="<?= $e($values['guest_count']) ?>"></label>
                    </div>
                    <label>Notas o peticiones especiales <textarea name="notes" maxlength="500" rows="3"><?= $e($values['notes']) ?></textarea></label>
                </fieldset>

                <fieldset>
                    <legend>Política de pago</legend>
                    <p class="reservation-policy">Tu apartado queda reservado por 3 días. Sube tu comprobante de transferencia, depósito u OXXO y el equipo liberará la residencia tras validarlo.</p>
                </fieldset>

                <button class="button button-gold" type="submit">Apartar residencia</button>
            </form>

            <aside class="admin-card reservation-quote">
                <h2><?= $e($stay['name']) ?></h2>
                <p class="account-intro"><?= $e($stay['capacity']) ?> huéspedes · <?= $e($stay['bedrooms']) ?> recámaras · <?= $e($stay['bathrooms']) ?> baños</p>
                <dl class="admin-data" id="quote">
                    <div><dt>Tarifa por noche</dt><dd>$<?= number_format((float) $stay['nightly_rate'], 2) ?></dd></div>
                    <div><dt>Noches</dt><dd data-quote="nights">—</dd></div>
                    <div><dt>Subtotal</dt><dd data-quote="subtotal">—</dd></div>
                    <div><dt>Limpieza</dt><dd>$<?= number_format((float) $stay['cleaning_fee'], 2) ?></dd></div>
                    <div><dt>Impuestos (19%)</dt><dd data-quote="tax">—</dd></div>
                    <div><dt>Total estimado</dt><dd><strong data-quote="total">—</strong></dd></div>
                </dl>
                <p class="reservation-policy">El total final se calcula al confirmar la reservación.</p>
            </aside>
        </div>
    <?php endif; ?>
</main>
<script>
(function () {
    var form = document.getElementById('reservation-form');
    if (!form) { return; }
    var nightly = parseFloat(form.dataset.nightly || '0');
    var cleaning = parseFloat(form.dataset.cleaning || '0');
    var money = function (value) {
        return '$' + value.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };
    function updateQuote() {
        var start = document.getElementById('check_in').value;
        var end = document.getElementById('check_out').value;
        if (!start || !end) { return; }
        var nights = Math.round((new Date(end) - new Date(start)) / 86400000);
        var output = document.querySelectorAll('#quote [data-quote]');
        if (nights <= 0) {
            output.forEach(function (node) { node.textContent = '—'; });
            return;
        }
        var subtotal = nightly * nights;
        var tax = subtotal * 0.19;
        var total = subtotal + cleaning + tax;
        document.querySelector('[data-quote="nights"]').textContent = nights;
        document.querySelector('[data-quote="subtotal"]').textContent = money(subtotal);
        document.querySelector('[data-quote="tax"]').textContent = money(tax);
        document.querySelector('[data-quote="total"]').textContent = money(total) + ' MXN';
    }
    form.addEventListener('input', updateQuote);
    form.addEventListener('change', updateQuote);
    updateQuote();
})();
</script>
</body>
</html>
