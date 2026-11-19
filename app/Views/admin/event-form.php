<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= !empty($event['id']) ? 'Editar evento' : 'Nuevo evento' ?> | Panel Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<?php $formUrl = $app['base_url'] . '/admin/eventos/crear'; ?>
<main class="catalog section-wrap admin-panel">
    <a class="account-link" href="<?= $e($app['base_url']) ?>/admin/eventos">← Volver a eventos</a>
    <h1><?= !empty($event['id']) ? 'Editar evento' : 'Nuevo evento' ?></h1>
    <?php require __DIR__ . '/_nav.php'; ?>
    <?php if ($error): ?><p class="account-error" role="alert"><?= $e($error) ?></p><?php endif; ?>

    <form method="post" action="<?= $e($formUrl) ?>" class="travel-form admin-form">
        <input type="hidden" name="token" value="<?= $e($token) ?>">
        <input type="hidden" name="id" value="<?= $e($values['id'] ?? 0) ?>">

        <fieldset>
            <legend>Información del evento</legend>
            <label>Nombre <input name="name" maxlength="160" required value="<?= $e($values['name'] ?? '') ?>"></label>
            <label>Residencia <select name="accommodation_id" required>
                <option value="">Selecciona una residencia</option>
                <?php foreach ($accommodations as $accommodation): ?>
                    <option value="<?= $e($accommodation['id']) ?>" <?= (int) ($values['accommodation_id'] ?? 0) === (int) $accommodation['id'] ? 'selected' : '' ?>><?= $e($accommodation['name']) ?></option>
                <?php endforeach; ?>
            </select></label>
            <div class="admin-fields">
                <label>Tipo <select name="event_type">
                    <?php foreach (\App\Models\Event::TYPES as $key => $label): ?>
                        <option value="<?= $e($key) ?>" <?= ($values['event_type'] ?? 'cultural') === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                    <?php endforeach; ?>
                </select></label>
                <label>Estado <select name="status">
                    <?php foreach (\App\Models\Event::STATUSES as $key => $label): ?>
                        <option value="<?= $e($key) ?>" <?= ($values['status'] ?? 'scheduled') === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                    <?php endforeach; ?>
                </select></label>
                <label>Fecha <input type="date" name="event_date" required value="<?= $e($values['event_date'] ?? date('Y-m-d')) ?>"></label>
            </div>
            <label class="stay-publish"><input type="checkbox" name="is_sporadic" <?= !empty($values['is_sporadic']) ? 'checked' : '' ?>> Evento esporádico (no se repite)</label>
            <label>Descripción <textarea name="description" rows="4"><?= $e($values['description'] ?? '') ?></textarea></label>
        </fieldset>

        <fieldset>
            <legend>Coordinación externa</legend>
            <label>Centro de atracción o sede <input name="attraction_center" maxlength="160" value="<?= $e($values['attraction_center'] ?? '') ?>"></label>
            <label>Proveedor externo <input name="provider" maxlength="160" value="<?= $e($values['provider'] ?? '') ?>"></label>
            <label>Huésped vinculado (opcional) <select name="user_id">
                <option value="">Sin huésped específico</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $e($user['id']) ?>" <?= (int) ($values['user_id'] ?? 0) === (int) $user['id'] ? 'selected' : '' ?>><?= $e($user['full_name'] . ' · ' . $user['email']) ?></option>
                <?php endforeach; ?>
            </select></label>
            <label>Reservación vinculada (opcional) <select name="reservation_id">
                <option value="">Sin reservación</option>
                <?php foreach ($reservations as $reservation): ?>
                    <option value="<?= $e($reservation['id']) ?>" <?= (int) ($values['reservation_id'] ?? 0) === (int) $reservation['id'] ? 'selected' : '' ?>><?= $e(($reservation['folio'] ?: '#' . $reservation['id']) . ' · ' . $reservation['guest_name'] . ' · ' . $reservation['accommodation_name']) ?></option>
                <?php endforeach; ?>
            </select></label>
        </fieldset>

        <div class="admin-actions">
            <button class="button button-gold" type="submit">Guardar evento</button>
            <a class="stay-action-link" href="<?= $e($app['base_url']) ?>/admin/eventos">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>
