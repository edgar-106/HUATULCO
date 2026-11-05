<?php
$locale = $app['default_locale'];
$copy = [
    'es' => ['nav_stays' => 'Alojamientos', 'nav_activities' => 'Actividades', 'nav_login' => 'Ingresar', 'eyebrow' => 'TU ESTANCIA EN HUATULCO', 'title' => 'Descubre Huatulco a tu manera', 'intro' => 'Encuentra dónde hospedarte y planea experiencias inolvidables junto al Pacífico.', 'explore' => 'Explorar alojamientos', 'activities' => 'Ver actividades', 'featured' => 'Lugares para descansar', 'from' => 'Desde'],
    'en' => ['nav_stays' => 'Stays', 'nav_activities' => 'Activities', 'nav_login' => 'Sign in', 'eyebrow' => 'YOUR STAY IN HUATULCO', 'title' => 'Discover Huatulco your way', 'intro' => 'Find a place to stay and plan unforgettable experiences by the Pacific.', 'explore' => 'Explore stays', 'activities' => 'See activities', 'featured' => 'Places to unwind', 'from' => 'From'],
    'fr' => ['nav_stays' => 'Hébergements', 'nav_activities' => 'Activités', 'nav_login' => 'Connexion', 'eyebrow' => 'VOTRE SÉJOUR À HUATULCO', 'title' => 'Découvrez Huatulco à votre façon', 'intro' => 'Trouvez un hébergement et préparez des expériences inoubliables au bord du Pacifique.', 'explore' => 'Voir les hébergements', 'activities' => 'Voir les activités', 'featured' => 'Des lieux pour se détendre', 'from' => 'À partir de'],
][$locale];
$e = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="<?= $e($locale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($app['name']) ?> | Estancias y experiencias</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="<?= $e($app['base_url']) ?>/" aria-label="Huatulco, inicio"><span class="brand-mark">H</span><span>HUATULCO<small>ESTANCIAS & EXPERIENCIAS</small></span></a>
    <nav aria-label="Navegación principal">
        <a href="<?= $e($app['base_url']) ?>/alojamientos"><?= $e($copy['nav_stays']) ?></a>
        <a href="<?= $e($app['base_url']) ?>/actividades"><?= $e($copy['nav_activities']) ?></a>
        <a class="nav-login" href="#cuenta"><?= $e($copy['nav_login']) ?></a>
        <div class="language-picker" aria-label="Idioma"><a href="?lang=es">ES</a><a href="?lang=en">EN</a><a href="?lang=fr">FR</a></div>
    </nav>
</header>
<main>
    <section class="hero">
        <div class="hero-content">
            <p class="eyebrow"><?= $e($copy['eyebrow']) ?></p>
            <h1><?= $e($copy['title']) ?></h1>
            <p class="hero-intro"><?= $e($copy['intro']) ?></p>
            <div class="hero-actions"><a class="button button-gold" href="<?= $e($app['base_url']) ?>/alojamientos"><?= $e($copy['explore']) ?></a><a class="button button-outline" href="#actividades"><?= $e($copy['activities']) ?></a></div>
        </div>
        <div class="hero-note"><span>09°08′ N · 96°07′ O</span><span>Bahías de Huatulco, Oaxaca</span></div>
    </section>
    <section class="featured section-wrap">
        <div class="section-heading"><div><p class="eyebrow">DESCANSA CERCA DEL MAR</p><h2><?= $e($copy['featured']) ?></h2></div><a class="text-link" href="<?= $e($app['base_url']) ?>/alojamientos">Ver todos <span>↗</span></a></div>
        <div class="stay-grid">
            <?php if ($accommodations === []): ?>
                <article class="empty-card"><span class="empty-icon">⌂</span><h3>Muy pronto</h3><p>Estamos preparando los primeros alojamientos para recibirte.</p></article>
                <article class="empty-card"><span class="empty-icon">⌂</span><h3>Tu rincón en Huatulco</h3><p>Casas, departamentos y habitaciones para cada viaje.</p></article>
                <article class="empty-card"><span class="empty-icon">⌂</span><h3>Experiencias locales</h3><p>Hospedaje y actividades reunidos en un solo lugar.</p></article>
            <?php else: foreach ($accommodations as $stay): ?>
                <article class="stay-card"><div class="stay-image" style="background-image:linear-gradient(180deg,transparent 55%,rgba(13,28,33,.6)),url('<?= $e($stay['cover_image'] ?: '') ?>')"><span class="stay-tag"><?= $e($stay['accommodation_type']) ?></span></div><div class="stay-info"><h3><?= $e($stay['name']) ?></h3><p><?= $e($stay['neighborhood']) ?> · Hasta <?= $e($stay['capacity']) ?> huéspedes</p><strong><?= $e($copy['from']) ?> $<?= number_format((float) $stay['nightly_rate'], 0) ?> MXN <small>/ noche</small></strong></div></article>
            <?php endforeach; endif; ?>
        </div>
    </section>
    <section class="experience-strip" id="actividades"><div><p class="eyebrow">MÁS QUE UN LUGAR PARA DORMIR</p><h2>Arma tu experiencia en Huatulco</h2></div><p>Explora actividades, organiza tus días y coordina los detalles de tu estancia desde un solo lugar.</p><a class="button button-dark" href="#actividades"><?= $e($copy['activities']) ?> <span>↗</span></a></section>
</main>
<footer><a class="brand brand-footer" href="<?= $e($app['base_url']) ?>/">HUATULCO<small>ESTANCIAS & EXPERIENCIAS</small></a><p>Bahías de Huatulco, Oaxaca · México</p><span>ES · EN · FR</span></footer>
</body>
</html>
