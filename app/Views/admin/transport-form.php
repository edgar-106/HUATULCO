<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= !empty($transport['id']) ? 'Editar transporte' : 'Nuevo transporte' ?> | Panel Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<?php $formUrl = $app['base_url'] . '/admin/transportes/crear'; ?>
<main class="catalog section-wrap admin-panel">
    <a class="account-link" href="<?= $e($app['base_url']) ?>/admin/transportes">← Volver a transporte</a>
    <h1><?= !empty($transport['id']) ? 'Editar transporte' : 'Nuevo transporte' ?></h1>
    <?php require __DIR__ . '/_nav.php'; ?>
    <?php if ($error): ?><p class="account-error" role="alert"><?= $e($error) ?></p><?php endif; ?>

    <form method="post" action="<?= $e($formUrl) ?>" class="travel-form admin-form">
        <input type="hidden" name="token" value="<?= $e($token) ?>">
        <input type="hidden" name="id" value="<?= $e($values['id'] ?? 0) ?>">

        <fieldset>
            <legend>Reservación y motivo</legend>
            <label>Reservación <select name="reservation_id" required>
                <option value="">Selecciona una reservación</option>
                <?php foreach ($reservations as $reservation): ?>
                    <option value="<?= $e($reservation['id']) ?>" <?= (int) ($values['reservation_id'] ?? 0) === (int) $reservation['id'] ? 'selected' : '' ?>><?= $e(($reservation['folio'] ?: '#' . $reservation['id']) . ' · ' . $reservation['guest_name'] . ' · ' . $reservation['accommodation_name'] . ' · ' . $reservation['check_in']) ?></option>
                <?php endforeach; ?>
            </select></label>
            <label>Motivo <input name="reason" maxlength="160" required placeholder="Traslado aeropuerto, tour, evento..." value="<?= $e($values['reason'] ?? '') ?>"></label>
        </fieldset>

        <fieldset>
            <legend>Vehículo y chofer</legend>
            <div class="admin-fields">
                <label>Tipo de vehículo <input name="vehicle_type" maxlength="80" required placeholder="Van, sedán, autobús..." value="<?= $e($values['vehicle_type'] ?? '') ?>"></label>
                <label>Placas <input name="plates" maxlength="20" value="<?= $e($values['plates'] ?? '') ?>"></label>
                <label>Chofer <input name="driver" maxlength="160" value="<?= $e($values['driver'] ?? '') ?>"></label>
            </div>
        </fieldset>

        <fieldset>
            <legend>Fechas y estado</legend>
            <div class="admin-fields">
                <label>Fecha de recogida <input type="date" name="pickup_date" value="<?= $e($values['pickup_date'] ?? '') ?>"></label>
                <label>Fecha de regreso <input type="date" name="return_date" value="<?= $e($values['return_date'] ?? '') ?>"></label>
                <label>Estado <select name="status">
                    <?php foreach (\App\Models\Transport::STATUSES as $key => $label): ?>
                        <option value="<?= $e($key) ?>" <?= ($values['status'] ?? 'assigned') === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                    <?php endforeach; ?>
                </select></label>
            </div>
            <label>Notas de logística <textarea name="notes" rows="3" maxlength="500"><?= $e($values['notes'] ?? '') ?></textarea></label>
        </fieldset>

        <div class="admin-actions">
            <button class="button button-gold" type="submit">Guardar transporte</button>
            <a class="stay-action-link" href="<?= $e($app['base_url']) ?>/admin/transportes">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>
