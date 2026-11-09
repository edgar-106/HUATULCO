<?php
$photos = require dirname(__DIR__, 3) . '/config/activity-photos.php';
$icons = [
    'acuatica' => 'M2 8c3 0 3-3 6-3s3 3 6 3 3-3 6-3 3 3 6 3M2 15c3 0 3-3 6-3s3 3 6 3 3-3 6-3 3 3 6 3M2 22c3 0 3-3 6-3s3 3 6 3 3-3 6-3 3 3 6 3',
    'aventura' => 'M2 24 12 6l7 12 3-5 6 11H2zM8.5 12l3.5 3 3.5-3',
    'aerea' => 'M3 12a12 8 0 0 1 24 0H3zm1 0 11 11 11-11M15 23v4',
    'cultura' => 'M5 24h20M7 24V13h16v11M3 13l12-8 12 8H3zM12 24v-6h6v6',
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Actividades | Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="<?= $e($app['base_url']) ?>/"><span class="brand-mark">H</span><span>HUATULCO<small>ESTANCIAS & EXPERIENCIAS</small></span></a>
    <nav>
        <a href="<?= $e($app['base_url']) ?>/alojamientos">Alojamientos</a>
        <a href="#mis-solicitudes">Mis solicitudes</a>
        <a class="nav-login" href="<?= $e($app['base_url']) ?>/cuenta/ingresar"><?= isset($_SESSION['user_id']) ? 'Mi cuenta' : 'Ingresar' ?></a>
    </nav>
</header>
<main class="catalog section-wrap">
    <p class="eyebrow">DESCUBRE LA REGIÓN</p>
    <h1>Actividades en Huatulco</h1>
    <p class="catalog-lead">Elige una experiencia, indica tu fecha y cuántas personas irán. El equipo revisará si hay disponibilidad.</p>

    <nav class="activity-section-nav" aria-label="Grupos de actividades">
        <?php foreach ($sections as $key => $title): ?>
            <a href="#<?= $e($key) ?>"><?= $e($title) ?></a>
        <?php endforeach; ?>
    </nav>

    <?php foreach ($sections as $key => $title): ?>
    <?php $activities = $groups[$key]; ?>
    <section class="activity-carousel" id="<?= $e($key) ?>" aria-labelledby="activity-title-<?= $e($key) ?>">
        <div class="activity-section-heading">
        <h2 id="activity-title-<?= $e($key) ?>"><?= $e($title) ?></h2>
        <?php if (count($activities) > 1): ?>
            <div class="activity-carousel-toolbar" hidden>
                <button type="button" class="activity-autoplay" aria-label="Pausar desplazamiento automático">Ⅱ</button>
                <button type="button" data-step="-1" aria-label="Actividades anteriores" aria-controls="activity-track-<?= $e($key) ?>">←</button>
                <button type="button" data-step="1" aria-label="Actividades siguientes" aria-controls="activity-track-<?= $e($key) ?>">→</button>
            </div>
        <?php endif; ?>
        </div>
        <?php if (!$activities): ?><p>Próximamente publicaremos nuevas experiencias.</p><?php endif; ?>
        <div class="activity-track" id="activity-track-<?= $e($key) ?>" tabindex="0" aria-label="<?= $e($title) ?>, desplaza para ver más actividades">
            <?php foreach ($activities as $activity): ?>
                <?php $photo = $photos[$activity['name']] ?? null; ?>
                <article class="activity-card">
                    <?php if ($photo): ?>
                        <img class="activity-photo" src="<?= $e($app['base_url']) ?>/assets/img/activities/<?= $e($photo[0]) ?>"
                            alt="<?= $e($photo[1]) ?>" width="900" height="600" loading="lazy" decoding="async">
                    <?php else: ?>
                        <div class="activity-art activity-art-<?= $e($key) ?>" aria-hidden="true">
                            <svg viewBox="0 0 30 30" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="<?= $e($icons[$key]) ?>"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <div class="activity-card-content">
                        <p class="eyebrow"><?= $e($activity['category']) ?></p>
                        <h3><?= $e($activity['name']) ?></h3>
                        <p><?= $e($activity['description']) ?></p>
                        <p><?= $activity['duration_minutes'] ? $e($activity['duration_minutes']) . ' min · ' : '' ?>
                            <?= (float) $activity['price'] > 0 ? '$' . number_format((float) $activity['price'], 2) . ' MXN' : 'Precio por confirmar' ?></p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="post" action="<?= $e($app['base_url']) ?>/actividades/solicitar" class="travel-form">
                                <input type="hidden" name="token" value="<?= $e($token) ?>">
                                <input type="hidden" name="activity_id" value="<?= $e($activity['id']) ?>">
                                <label>Fecha <input name="date" type="date" min="<?= $e(date('Y-m-d')) ?>" required></label>
                                <label>Personas <input name="participants" type="number" min="1" max="<?= $e($activity['capacity'] ?? 65535) ?>" value="1" required></label>
                                <button class="button button-gold">Consultar disponibilidad</button>
                            </form>
                        <?php else: ?>
                            <a class="button button-gold" href="<?= $e($app['base_url']) ?>/cuenta/ingresar">Ingresa para solicitar</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endforeach; ?>

    <section id="mis-solicitudes">
        <h2>Mis solicitudes</h2>
        <?php if ($notice): ?><p role="status"><?= $e($notice) ?></p><?php endif; ?>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <p>Ingresa para consultar el estado de tus solicitudes.</p>
        <?php elseif (!$requests): ?>
            <p>Aún no tienes solicitudes.</p>
        <?php endif; ?>
        <?php $labels = ['pending' => 'Pendiente de revisión', 'confirmed' => 'Confirmada', 'declined' => 'Rechazada']; ?>
        <?php foreach ($requests as $request): ?>
            <article>
                <h3><?= $e($request['name']) ?> · <?= $e($request['requested_date']) ?></h3>
                <p><?= $e($request['participants']) ?> persona(s) · <strong><?= $e($labels[$request['status']]) ?></strong></p>
                <?php if ($request['response']): ?><p><?= $e($request['response']) ?></p><?php endif; ?>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<script src="<?= $e($app['base_url']) ?>/assets/js/activity-carousel.js" defer></script>
<footer><a class="brand brand-footer" href="<?= $e($app['base_url']) ?>/">HUATULCO<small>ESTANCIAS & EXPERIENCIAS</small></a><p>Bahías de Huatulco, Oaxaca · México</p></footer>
</body>
</html>
