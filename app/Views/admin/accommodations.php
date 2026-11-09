<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrar alojamientos | Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<main class="catalog section-wrap">
    <a href="<?= $e($app['base_url']) ?>/">Inicio</a>
    <a href="<?= $e($app['base_url']) ?>/admin/actividades">Administrar actividades</a>
    <h1>Administrar alojamientos</h1>
    <?php if ($error): ?><p role="alert"><?= $e($error) ?></p><?php endif; ?>
    <ul>
        <?php foreach ($stays as $stay): ?>
            <li><a href="?editar=<?= $e($stay['id']) ?>"><?= $e($stay['name']) ?></a>
                <?= $stay['is_published'] ? '· Publicado' : '· Borrador' ?></li>
        <?php endforeach; ?>
    </ul>
    <h2><?= $edit ? 'Editar alojamiento' : 'Nuevo alojamiento' ?></h2>
    <?php if ($edit): ?><a href="<?= $e($app['base_url']) ?>/admin/alojamientos">Nuevo alojamiento</a><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="travel-form">
        <input type="hidden" name="token" value="<?= $e($token) ?>">
        <input type="hidden" name="id" value="<?= $e($edit['id'] ?? 0) ?>">
        <label>Nombre <input name="name" maxlength="160" value="<?= $e($edit['name'] ?? '') ?>" required></label>
        <label>Tipo <select name="accommodation_type">
            <?php foreach ($types as $type): ?>
                <option value="<?= $e($type) ?>" <?= ($edit['accommodation_type'] ?? '') === $type ? 'selected' : '' ?>><?= $e(ucfirst($type)) ?></option>
            <?php endforeach; ?>
        </select></label>
        <label>Descripción <textarea name="description"><?= $e($edit['description'] ?? '') ?></textarea></label>
        <label>Zona o dirección <input name="neighborhood" maxlength="120" value="<?= $e($edit['neighborhood'] ?? '') ?>"></label>
        <label>Latitud <input type="number" name="latitude" step="0.0000001" min="-90" max="90" value="<?= $e($edit['latitude'] ?? '') ?>"></label>
        <label>Longitud <input type="number" name="longitude" step="0.0000001" min="-180" max="180" value="<?= $e($edit['longitude'] ?? '') ?>"></label>
        <label>Huéspedes <input type="number" name="capacity" min="1" value="<?= $e($edit['capacity'] ?? 1) ?>" required></label>
        <label>Recámaras <input type="number" name="bedrooms" min="0" value="<?= $e($edit['bedrooms'] ?? 0) ?>" required></label>
        <label>Baños <input type="number" name="bathrooms" min="0" value="<?= $e($edit['bathrooms'] ?? 0) ?>" required></label>
        <label>Tarifa por noche (MXN) <input type="number" name="nightly_rate" min="0" step="0.01" value="<?= $e($edit['nightly_rate'] ?? '') ?>" required></label>
        <label>Fotos <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple></label>
        <label><input type="checkbox" name="is_published" <?= !empty($edit['is_published']) ? 'checked' : '' ?>> Mostrar en el catálogo</label>
        <button type="submit">Guardar</button>
        <?php if ($edit): ?><button type="submit" name="action" value="deactivate" formnovalidate>Dar de baja</button><?php endif; ?>
    </form>
</main>
</body>
</html>
