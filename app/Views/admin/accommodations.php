<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrar alojamientos | Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<?php $panelUrl = $app['base_url'] . '/admin/alojamientos'; ?>
<main class="catalog section-wrap stay-admin">
    <nav class="stay-actions" aria-label="Panel de administración">
        <a class="account-link" href="<?= $e($panelUrl) ?>">Panel de alojamientos</a>
        <a class="account-link" href="<?= $e($app['base_url']) ?>/admin/actividades">Administrar actividades</a>
    </nav>
    <?php if ($mode === 'list'): ?>
        <h1>Administrar alojamientos</h1>
        <p class="catalog-lead">Gestiona las propiedades, sus precios y su visibilidad en el catálogo.</p>
        <div class="stay-actions">
            <a class="button button-gold" href="<?= $e($panelUrl) ?>?nuevo=1">Nuevo alojamiento</a>
            <a class="stay-action-link" href="<?= $e($panelUrl) ?><?= $includeInactive ? '' : '?inactivos=1' ?>"><?= $includeInactive ? 'Ver solo activos' : 'Incluir alojamientos inactivos' ?></a>
        </div>
        <?php if (!$stays): ?><p>No hay alojamientos registrados.</p><?php endif; ?>
        <div class="stay-admin-list">
            <?php foreach ($stays as $stay): ?>
                <article class="stay-admin-row">
                    <div>
                        <h2><?= $e($stay['name']) ?></h2>
                        <p><?= $e($stay['neighborhood']) ?></p>
                    </div>
                    <p><?= $e(ucfirst($stay['accommodation_type'])) ?><br>
                        <?= $e($stay['capacity']) ?> huéspedes · <?= $e($stay['bedrooms']) ?> recámaras · <?= $e($stay['bathrooms']) ?> baños</p>
                    <div>
                        <strong>$<?= number_format((float) $stay['nightly_rate'], 2) ?> MXN</strong><br>
                        <span class="stay-status"><?= !$stay['is_active'] ? 'Inactivo' : ($stay['is_published'] ? 'Publicado' : 'Borrador') ?></span>
                    </div>
                    <div class="stay-actions">
                        <?php if ($stay['is_active'] && $stay['is_published']): ?>
                            <a class="stay-action-link" href="<?= $e($app['base_url']) ?>/alojamientos/detalle?id=<?= $e($stay['id']) ?>">Ver ficha</a>
                        <?php endif; ?>
                        <?php if ($stay['is_active']): ?>
                            <a class="stay-action-link" href="<?= $e($panelUrl) ?>?editar=<?= $e($stay['id']) ?>">Editar</a>
                            <form method="post" action="<?= $e($panelUrl) ?>">
                                <input type="hidden" name="token" value="<?= $e($token) ?>">
                                <input type="hidden" name="id" value="<?= $e($stay['id']) ?>">
                                <button class="stay-action-link stay-action-danger" name="action" value="deactivate">Dar de baja</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <h1><?= $mode === 'edit' ? 'Editar alojamiento' : 'Nuevo alojamiento' ?></h1>
        <?php if ($error): ?><p class="account-error" role="alert"><?= $e($error) ?></p><?php endif; ?>
        <form method="post" action="<?= $e($panelUrl) ?>" enctype="multipart/form-data" class="travel-form stay-admin-form">
            <input type="hidden" name="token" value="<?= $e($token) ?>">
            <input type="hidden" name="id" value="<?= $e($edit['id'] ?? 0) ?>">
            <div class="stay-admin-column">
                <fieldset>
                    <legend>Información principal</legend>
                    <label>Nombre <input name="name" maxlength="160" value="<?= $e($edit['name'] ?? '') ?>" required></label>
                    <label>Tipo <select name="accommodation_type">
                        <?php foreach ($types as $type): ?>
                            <option value="<?= $e($type) ?>" <?= ($edit['accommodation_type'] ?? '') === $type ? 'selected' : '' ?>><?= $e(ucfirst($type)) ?></option>
                        <?php endforeach; ?>
                    </select></label>
                    <label>Descripción <textarea name="description" rows="4"><?= $e($edit['description'] ?? '') ?></textarea></label>
                </fieldset>
                <fieldset>
                    <legend>Capacidad y espacios</legend>
                    <div class="stay-admin-fields">
                        <label>Huéspedes <input type="number" name="capacity" min="1" max="65535" value="<?= $e($edit['capacity'] ?? 1) ?>" required></label>
                        <label>Recámaras <input type="number" name="bedrooms" min="0" max="65535" value="<?= $e($edit['bedrooms'] ?? 0) ?>" required></label>
                        <label>Baños <input type="number" name="bathrooms" min="0" max="65535" value="<?= $e($edit['bathrooms'] ?? 0) ?>" required></label>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Galería de fotografías</legend>
                    <?php if ($photos): ?>
                        <div class="stay-admin-photos">
                            <?php foreach ($photos as $photo): ?>
                                <img src="<?= $e(str_starts_with($photo, '/uploads/') ? $app['base_url'] . $photo : $photo) ?>" alt="" loading="lazy">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <label>Agregar fotos <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple></label>
                    <small>JPG, PNG o WEBP, hasta 5 MB por foto.</small>
                </fieldset>
            </div>
            <div class="stay-admin-column">
                <fieldset>
                    <legend>Tarifa y publicación</legend>
                    <label>Tarifa por noche (MXN) <input type="number" name="nightly_rate" min="0" step="0.01" value="<?= $e($edit['nightly_rate'] ?? '') ?>" required></label>
                    <label class="stay-publish"><input type="checkbox" name="is_published" <?= !empty($edit['is_published']) ? 'checked' : '' ?>> Mostrar en el catálogo</label>
                </fieldset>
                <fieldset>
                    <legend>Ubicación en Huatulco</legend>
                    <label>Dirección o referencia <input name="neighborhood" maxlength="120" value="<?= $e($edit['neighborhood'] ?? '') ?>"></label>
                    <div class="stay-admin-fields">
                        <label>Latitud <input type="number" name="latitude" step="0.0000001" min="-90" max="90" value="<?= $e($edit['latitude'] ?? '') ?>"></label>
                        <label>Longitud <input type="number" name="longitude" step="0.0000001" min="-180" max="180" value="<?= $e($edit['longitude'] ?? '') ?>"></label>
                    </div>
                    <label>Enlace al recorrido virtual <input type="url" name="virtual_tour_url" maxlength="500" value="<?= $e($edit['virtual_tour_url'] ?? '') ?>"></label>
                </fieldset>
                <div class="stay-actions">
                    <button class="button button-gold" type="submit">Guardar alojamiento</button>
                    <a class="stay-action-link" href="<?= $e($panelUrl) ?>">Cancelar</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
