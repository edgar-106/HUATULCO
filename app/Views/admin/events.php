<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eventos | Panel Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<main class="catalog section-wrap admin-panel">
    <h1>Eventos</h1>
    <?php require __DIR__ . '/_nav.php'; ?>
    <?php if ($notice): ?><p class="admin-notice" role="status"><?= $e($notice) ?></p><?php endif; ?>
    <p class="catalog-lead">Coordina eventos culturales, deportivos y sociales, fijos o esporádicos, con su centro de atracción y proveedor externo.</p>

    <div class="admin-actions">
        <a class="button button-gold" href="<?= $e($app['base_url']) ?>/admin/eventos/crear">Nuevo evento</a>
    </div>

    <form method="get" action="<?= $e($app['base_url']) ?>/admin/eventos" class="admin-filters">
        <label>Residencia
            <select name="alojamiento">
                <option value="">Todas</option>
                <?php foreach ($accommodations as $accommodation): ?>
                    <option value="<?= $e($accommodation['id']) ?>" <?= (int) $accommodationId === (int) $accommodation['id'] ? 'selected' : '' ?>><?= $e($accommodation['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Tipo
            <select name="tipo">
                <option value="">Todos</option>
                <?php foreach (\App\Models\Event::TYPES as $key => $label): ?>
                    <option value="<?= $e($key) ?>" <?= $type === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Modalidad
            <select name="modalidad">
                <option value="">Todas</option>
                <option value="fijo" <?= $sporadic === false ? 'selected' : '' ?>>Fijo</option>
                <option value="esporadico" <?= $sporadic === true ? 'selected' : '' ?>>Esporádico</option>
            </select>
        </label>
        <label>Estado
            <select name="estado">
                <option value="">Todos</option>
                <?php foreach (\App\Models\Event::STATUSES as $key => $label): ?>
                    <option value="<?= $e($key) ?>" <?= $status === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button button-dark" type="submit">Filtrar</button>
        <a class="stay-action-link" href="<?= $e($app['base_url']) ?>/admin/eventos">Limpiar</a>
    </form>

    <?php if (!$events): ?>
        <p class="admin-empty">No hay eventos con estos criterios.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Evento</th><th>Residencia</th><th>Tipo</th><th>Modalidad</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td>
                            <strong><?= $e($event['name']) ?></strong>
                            <?php if ($event['reservation_folio']): ?><br><small>Reservación <?= $e($event['reservation_folio']) ?></small><?php endif; ?>
                            <?php if ($event['provider']): ?><br><small>Proveedor: <?= $e($event['provider']) ?></small><?php endif; ?>
                        </td>
                        <td><?= $e($event['accommodation_name']) ?></td>
                        <td><?= $e(\App\Models\Event::typeLabel($event['event_type'])) ?></td>
                        <td><?= $event['is_sporadic'] ? 'Esporádico' : 'Fijo' ?></td>
                        <td><?= $e($event['event_date']) ?></td>
                        <td><span class="status-badge status-<?= $e($event['status']) ?>"><?= $e(\App\Models\Event::statusLabel($event['status'])) ?></span></td>
                        <td>
                            <div class="admin-actions">
                                <a class="stay-action-link" href="<?= $e($app['base_url']) ?>/admin/eventos/crear?id=<?= $e($event['id']) ?>">Editar</a>
                                <form method="post" action="<?= $e($app['base_url']) ?>/admin/eventos/eliminar" onsubmit="return confirm('¿Eliminar este evento?');">
                                    <input type="hidden" name="token" value="<?= $e($token) ?>">
                                    <input type="hidden" name="id" value="<?= $e($event['id']) ?>">
                                    <button class="stay-action-link stay-action-danger" type="submit">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="admin-count"><?= count($events) ?> evento(s).</p>
    <?php endif; ?>
</main>
</body>
</html>
