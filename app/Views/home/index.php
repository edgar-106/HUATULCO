<?php
$locale = $app['default_locale'];
$copy = [
    'es' => ['nav_stays' => 'Alojamientos', 'nav_activities' => 'Actividades', 'nav_login' => 'Ingresar', 'eyebrow' => 'TU ESTANCIA EN HUATULCO', 'title' => 'Descubre Huatulco a tu manera', 'intro' => 'Encuentra dónde hospedarte y planea experiencias inolvidables junto al Pacífico.', 'explore' => 'Explorar alojamientos', 'activities' => 'Ver actividades', 'featured' => 'Lugares para descansar', 'from' => 'Desde', 'map_title_1' => 'Del espacio', 'map_title_2' => 'hasta Huatulco.', 'map_count' => 'residencias ubicadas', 'map_caption' => 'IMAGEN SATELITAL · BAHÍAS DE HUATULCO', 'map_hint' => 'Las residencias aparecerán en este mapa.', 'map_hint_detail' => 'El administrador debe agregar las coordenadas de cada alojamiento para mostrar sus puntos exactos.'],
    'en' => ['nav_stays' => 'Stays', 'nav_activities' => 'Activities', 'nav_login' => 'Sign in', 'eyebrow' => 'YOUR STAY IN HUATULCO', 'title' => 'Discover Huatulco your way', 'intro' => 'Find a place to stay and plan unforgettable experiences by the Pacific.', 'explore' => 'Explore stays', 'activities' => 'See activities', 'featured' => 'Places to unwind', 'from' => 'From', 'map_title_1' => 'From space', 'map_title_2' => 'to Huatulco.', 'map_count' => 'residences located', 'map_caption' => 'SATELLITE IMAGERY · HUATULCO BAYS', 'map_hint' => 'Residences will appear on this map.', 'map_hint_detail' => 'The administrator needs to add each property’s coordinates to show its exact location.'],
    'fr' => ['nav_stays' => 'Hébergements', 'nav_activities' => 'Activités', 'nav_login' => 'Connexion', 'eyebrow' => 'VOTRE SÉJOUR À HUATULCO', 'title' => 'Découvrez Huatulco à votre façon', 'intro' => 'Trouvez un hébergement et préparez des expériences inoubliables au bord du Pacifique.', 'explore' => 'Voir les hébergements', 'activities' => 'Voir les activités', 'featured' => 'Des lieux pour se détendre', 'from' => 'À partir de', 'map_title_1' => 'De l’espace', 'map_title_2' => 'à Huatulco.', 'map_count' => 'résidences localisées', 'map_caption' => 'IMAGE SATELLITE · BAIES DE HUATULCO', 'map_hint' => 'Les résidences apparaîtront sur cette carte.', 'map_hint_detail' => 'L’administrateur devra ajouter les coordonnées de chaque logement pour afficher son emplacement exact.'],
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
</head>
<body>
<?php require __DIR__ . '/../shared/header.php'; ?>
<main>
    <section class="map-hero" aria-label="Mapa aéreo interactivo de Huatulco">
        <div id="huatulco-map" class="huatulco-map" role="application" aria-label="Mapa satelital con la ubicación de los alojamientos"></div>
        <div class="map-shade"></div>
        <div class="map-copy">
            <p class="eyebrow"><?= $e($copy['eyebrow']) ?></p>
            <h1><?= $e($copy['map_title_1']) ?><br><?= $e($copy['map_title_2']) ?></h1>
            <p><?= $e($copy['intro']) ?></p>
            <a class="button button-gold" href="<?= $e($app['base_url']) ?>/alojamientos"><?= $e($copy['explore']) ?> <span>↗</span></a>
        </div>
        <div class="map-location"><span class="location-pulse"></span><div><strong>HUATULCO, OAXACA</strong><small>15°46′ N · 96°08′ O</small></div></div>
        <div class="map-counter"><span class="counter-dot"></span><strong><?= count($mapAccommodations) ?></strong><span><?= $e($copy['map_count']) ?></span></div>
        <div class="map-controls"><button type="button" id="map-zoom-in" aria-label="Acercar mapa">+</button><button type="button" id="map-zoom-out" aria-label="Alejar mapa">−</button></div>
        <div class="map-caption"><?= $e($copy['map_caption']) ?></div>
    </section>
    <?php if ($mapAccommodations === []): ?><aside class="map-hint"><span>◎</span><p><strong><?= $e($copy['map_hint']) ?></strong> <?= $e($copy['map_hint_detail']) ?></p><a href="<?= $e($app['base_url']) ?>/alojamientos"><?= $e($copy['nav_stays']) ?></a></aside><?php endif; ?>
    <section class="featured section-wrap">
        <div class="section-heading"><div><p class="eyebrow">DESCANSA CERCA DEL MAR</p><h2><?= $e($copy['featured']) ?></h2></div><a class="text-link" href="<?= $e($app['base_url']) ?>/alojamientos">Ver todos <span>↗</span></a></div>
        <div class="stay-grid">
            <?php if ($accommodations === []): ?>
                <article class="empty-card"><span class="empty-icon">⌂</span><h3>Muy pronto</h3><p>Estamos preparando los primeros alojamientos para recibirte.</p></article>
                <article class="empty-card"><span class="empty-icon">⌂</span><h3>Tu rincón en Huatulco</h3><p>Casas, departamentos y habitaciones para cada viaje.</p></article>
                <article class="empty-card"><span class="empty-icon">⌂</span><h3>Experiencias locales</h3><p>Hospedaje y actividades reunidos en un solo lugar.</p></article>
            <?php else: foreach ($accommodations as $stay): ?>
                <article class="stay-card"><div class="stay-image" style="background-image:linear-gradient(180deg,transparent 55%,rgba(13,28,33,.6)),url('<?= $e($stay['cover_image'] ?: '') ?>')"><span class="stay-tag"><?= $e($stay['accommodation_type']) ?></span></div><div class="stay-info"><h3><a class="stay-detail-link" href="<?= $e($app['base_url']) ?>/alojamientos/detalle?id=<?= $e($stay['id']) ?>"><?= $e($stay['name']) ?></a></h3><p><?= $e($stay['neighborhood']) ?> · Hasta <?= $e($stay['capacity']) ?> huéspedes</p><strong><?= $e($copy['from']) ?> $<?= number_format((float) $stay['nightly_rate'], 0) ?> MXN <small>/ noche</small></strong></div></article>
            <?php endforeach; endif; ?>
        </div>
    </section>
    <section class="experience-strip" id="actividades"><div><p class="eyebrow">MÁS QUE UN LUGAR PARA DORMIR</p><h2>Arma tu experiencia en Huatulco</h2></div><p>Explora actividades, organiza tus días y coordina los detalles de tu estancia desde un solo lugar.</p><a class="button button-dark" href="#actividades"><?= $e($copy['activities']) ?> <span>↗</span></a></section>
</main>
<footer><a class="brand brand-footer" href="<?= $e($app['base_url']) ?>/">HUATULCO<small>ESTANCIAS & EXPERIENCIAS</small></a><p>Bahías de Huatulco, Oaxaca · México</p><span>ES · EN · FR</span></footer>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(() => {
    const properties = <?= json_encode(array_map(static fn ($home) => [
        'name' => $home['name'], 'type' => $home['accommodation_type'],
        'area' => $home['neighborhood'], 'price' => (float) $home['nightly_rate'],
        'lat' => (float) $home['latitude'], 'lng' => (float) $home['longitude'],
    ], $mapAccommodations), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const map = L.map('huatulco-map', {zoomControl: false, scrollWheelZoom: false, attributionControl: true}).setView([18, -102], 3);
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19, attribution: 'Tiles &copy; Esri — Source: Esri, Maxar, Earthstar Geographics, and the GIS User Community'
    }).addTo(map);
    const huatulco = [15.775, -96.13];
    const prefersReducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        map.setView(huatulco, properties.length ? 13 : 11);
    } else {
        window.setTimeout(() => map.flyTo(huatulco, properties.length ? 13 : 11, {duration: 4.5, easeLinearity: 0.18}), 450);
    }
    const icon = L.divIcon({className: 'residence-marker-wrap', html: '<span class="residence-marker"><i></i></span>', iconSize: [34, 42], iconAnchor: [17, 38], popupAnchor: [0, -36]});
    properties.forEach((home) => {
        const marker = L.marker([home.lat, home.lng], {icon}).addTo(map);
        const popup = document.createElement('div');
        popup.className = 'residence-popup';
        const label = document.createElement('small'); label.textContent = home.type + (home.area ? ' · ' + home.area : '');
        const title = document.createElement('strong'); title.textContent = home.name;
        const price = document.createElement('span'); price.textContent = 'Desde $' + new Intl.NumberFormat('es-MX').format(home.price) + ' MXN / noche';
        const link = document.createElement('a'); link.href = '<?= $e($app['base_url']) ?>/alojamientos'; link.textContent = 'Ver alojamiento →';
        popup.append(label, title, price, link);
        marker.bindPopup(popup);
    });
    document.getElementById('map-zoom-in').addEventListener('click', () => map.zoomIn());
    document.getElementById('map-zoom-out').addEventListener('click', () => map.zoomOut());
    window.addEventListener('resize', () => map.invalidateSize());
})();
</script>
</body>
</html>
