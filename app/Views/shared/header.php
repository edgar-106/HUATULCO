<?php
$siteNav = [
    [$app['base_url'] . '/alojamientos', $copy['nav_stays'] ?? 'Alojamientos'],
    [$app['base_url'] . '/actividades', $copy['nav_activities'] ?? 'Actividades'],
];
if (!empty($showRequestsLink)) {
    $siteNav[] = ['#mis-solicitudes', 'Mis solicitudes'];
}
$accountLabel = isset($_SESSION['user_id']) ? 'Mi cuenta' : ($copy['nav_login'] ?? 'Ingresar');
?>
<header class="site-header responsive-site-header">
    <a class="brand" href="<?= $e($app['base_url']) ?>/" aria-label="Huatulco, inicio">
        <span class="brand-mark">H</span><span>HUATULCO<small>ESTANCIAS &amp; EXPERIENCIAS</small></span>
    </a>
    <nav class="desktop-site-nav" aria-label="Navegación principal">
        <?php foreach ($siteNav as [$url, $label]): ?><a href="<?= $e($url) ?>"><?= $e($label) ?></a><?php endforeach; ?>
        <a class="nav-login" href="<?= $e($app['base_url']) ?>/cuenta/ingresar"><?= $e($accountLabel) ?></a>
        <?php if (isset($locale)): ?>
            <div class="language-picker" aria-label="Idioma"><a href="?lang=es">ES</a><a href="?lang=en">EN</a><a href="?lang=fr">FR</a></div>
        <?php endif; ?>
    </nav>
    <details class="mobile-site-menu">
        <summary>☰ <span>Menú</span></summary>
        <nav aria-label="Navegación móvil">
            <?php foreach ($siteNav as [$url, $label]): ?><a href="<?= $e($url) ?>"><?= $e($label) ?></a><?php endforeach; ?>
            <a href="<?= $e($app['base_url']) ?>/cuenta/ingresar"><?= $e($accountLabel) ?></a>
            <?php if (isset($locale)): ?>
                <div class="mobile-languages" aria-label="Idioma"><a href="?lang=es">ES</a><a href="?lang=en">EN</a><a href="?lang=fr">FR</a></div>
            <?php endif; ?>
        </nav>
    </details>
</header>
