<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrar actividades | Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<main class="catalog section-wrap">
    <nav><a href="<?= $e($app['base_url']) ?>/actividades">Catálogo público</a> ·
        <a href="<?= $e($app['base_url']) ?>/admin/alojamientos">Administrar alojamientos</a></nav>
    <h1>Administrar actividades</h1>
    <?php if ($notice): ?><p role="status"><?= $e($notice) ?></p><?php endif; ?>
    <section id="solicitudes">
        <h2>Solicitudes de disponibilidad</h2>
        <?php if (!$requests): ?><p>Aún no hay solicitudes.</p><?php endif; ?>
        <?php foreach ($requests as $request): ?>
            <article>
                <h3><?= $e($request['activity']) ?> · <?= $e($request['requested_date']) ?><?= $request['requested_time'] !== null ? ' · ' . $e(substr($request['requested_time'], 0, 5)) : '' ?></h3>
                <p>
                    <?= $e($request['full_name']) ?> (<?= $e($request['email']) ?>) ·
                    <?php if ($request['adults'] !== null): ?>
                        <?= $e($request['adults']) ?> adulto(s) y <?= $e($request['children']) ?> niño(s)
                    <?php else: ?>
                        <?= $e($request['participants']) ?> persona(s)
                    <?php endif; ?>
                </p>
                <?php if ($request['status'] === 'pending'): ?>
                    <form method="post" action="<?= $e($app['base_url']) ?>/admin/actividades/revisar" class="travel-form">
                        <input type="hidden" name="token" value="<?= $e($token) ?>">
                        <input type="hidden" name="id" value="<?= $e($request['id']) ?>">
                        <label>Mensaje para el cliente (opcional) <textarea name="response" maxlength="500"></textarea></label>
                        <?php if ($request['requested_date'] . ' ' . ($request['requested_time'] ?? '23:59:59') >= $now): ?>
                            <button name="decision" value="confirmed">Confirmar disponibilidad</button>
                        <?php endif; ?>
                        <button name="decision" value="declined">Rechazar solicitud</button>
                    </form>
                <?php else: ?>
                    <p><?= $request['status'] === 'confirmed' ? 'Confirmada' : 'Rechazada' ?><?= $request['response'] ? ' · ' . $e($request['response']) : '' ?></p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </section>
    <section>
        <h2>Catálogo</h2>
        <ul>
            <?php foreach ($activities as $activity): ?>
                <li><a href="?editar=<?= $e($activity['id']) ?>#actividad"><?= $e($activity['name']) ?></a>
                    · <?= $e($sections[$activity['catalog_section']] ?? 'Sin grupo asignado') ?>
                    · <?= $activity['is_published'] ? 'Publicada' : 'Borrador' ?></li>
            <?php endforeach; ?>
        </ul>
        <h3 id="actividad"><?= $edit ? 'Editar actividad' : 'Nueva actividad' ?></h3>
        <?php if ($edit): ?><a href="<?= $e($app['base_url']) ?>/admin/actividades#actividad">Nueva actividad</a><?php endif; ?>
        <form method="post" action="<?= $e($app['base_url']) ?>/admin/actividades" class="travel-form">
            <input type="hidden" name="token" value="<?= $e($token) ?>">
            <input type="hidden" name="id" value="<?= $e($edit['id'] ?? 0) ?>">
            <label>Nombre <input name="name" required maxlength="160" value="<?= $e($edit['name'] ?? '') ?>"></label>
            <label>Categoría <select name="category">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $e($category) ?>" <?= ($edit['category'] ?? '') === $category ? 'selected' : '' ?>><?= $e(ucfirst($category)) ?></option>
                <?php endforeach; ?>
            </select></label>
            <label>Grupo en el catálogo <select name="catalog_section" required>
                <option value="">Selecciona un grupo</option>
                <?php foreach ($sections as $key => $label): ?>
                    <option value="<?= $e($key) ?>" <?= ($edit['catalog_section'] ?? '') === $key ? 'selected' : '' ?>><?= $e($label) ?></option>
                <?php endforeach; ?>
            </select></label>
            <label>Descripción <textarea name="description"><?= $e($edit['description'] ?? '') ?></textarea></label>
            <label>Precio en MXN (opcional) <input name="price" type="number" min="0" step="0.01" value="<?= $e($edit['price'] ?? '') ?>"></label>
            <label>Duración en minutos (opcional) <input name="duration" type="number" min="1" max="65535" value="<?= $e($edit['duration_minutes'] ?? '') ?>"></label>
            <label>Máximo de personas por solicitud (opcional) <input name="capacity" type="number" min="1" max="65535" value="<?= $e($edit['capacity'] ?? '') ?>"></label>
            <label><input name="is_published" type="checkbox" <?= !empty($edit['is_published']) ? 'checked' : '' ?>> Mostrar en el catálogo</label>
            <button>Guardar actividad</button>
        </form>
    </section>
</main>
</body>
</html>
