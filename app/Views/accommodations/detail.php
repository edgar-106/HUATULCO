<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($stay['name'] ?? 'Alojamiento no disponible') ?> | Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<main class="catalog section-wrap stay-detail">
    <a class="account-link" href="<?= $e($app['base_url']) ?>/alojamientos">← Volver a alojamientos</a>
    <?php if (!$stay): ?>
        <h1>Alojamiento no disponible</h1>
    <?php else: ?>
        <h1><?= $e($stay['name']) ?></h1>
        <p class="catalog-lead"><?= $e($stay['neighborhood']) ?></p>
        <?php if ($stay['photos']): ?>
            <div class="stay-detail-gallery">
                <?php foreach ($stay['photos'] as $index => $photo): ?>
                    <?php $photoUrl = str_starts_with($photo, '/uploads/') ? $app['base_url'] . $photo : $photo; ?>
                    <?php if (!str_starts_with($photo, '/uploads/') && !in_array(parse_url($photo, PHP_URL_SCHEME), ['http', 'https'], true)) continue; ?>
                    <a href="<?= $e($photoUrl) ?>" aria-label="Ver foto <?= $e($index + 1) ?> de <?= $e($stay['name']) ?>">
                        <img src="<?= $e($photoUrl) ?>" alt="<?= $e($stay['name']) ?>" <?= $index ? 'loading="lazy"' : '' ?>>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <dl class="stay-specs">
            <div><dt>Capacidad</dt><dd><?= $e($stay['capacity']) ?> huéspedes</dd></div>
            <div><dt>Habitaciones</dt><dd><?= $e($stay['bedrooms']) ?> recámaras</dd></div>
            <div><dt>Baños</dt><dd><?= $e($stay['bathrooms']) ?> baños</dd></div>
            <div><dt>Tipo de estancia</dt><dd><?= $e(ucfirst($stay['accommodation_type'])) ?></dd></div>
        </dl>
        <div class="stay-detail-info">
            <section>
                <h2>Acerca de este alojamiento</h2>
                <p><?= nl2br($e($stay['description'])) ?></p>
            </section>
            <aside>
                <h2>Tarifa por noche</h2>
                <p><strong>$<?= number_format((float) $stay['nightly_rate'], 2) ?> MXN</strong></p>
                <?php if ($stay['virtual_tour_url'] && in_array(parse_url($stay['virtual_tour_url'], PHP_URL_SCHEME), ['https', 'http'], true)): ?>
                    <a class="button button-dark" href="<?= $e($stay['virtual_tour_url']) ?>">Ver recorrido virtual</a>
                <?php endif; ?>
                <?php if ($stay['latitude'] !== null && $stay['longitude'] !== null): ?>
                    <a class="button button-gold" href="https://www.google.com/maps/dir/?api=1&amp;destination=<?= $e(rawurlencode($stay['latitude'] . ',' . $stay['longitude'])) ?>">Cómo llegar</a>
                <?php endif; ?>
            </aside>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
