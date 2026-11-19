<?php
$adminLinks = [
    ['/admin/reservaciones', 'Reservaciones'],
    ['/admin/pagos', 'Pagos'],
    ['/admin/eventos', 'Eventos'],
    ['/admin/transportes', 'Transporte'],
    ['/admin/alojamientos', 'Alojamientos'],
    ['/admin/actividades', 'Actividades'],
];
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = rtrim((string) $app['base_url'], '/');
if ($basePath !== '' && str_starts_with($currentPath, $basePath)) {
    $currentPath = substr($currentPath, strlen($basePath)) ?: '/';
}
?>
<nav class="admin-nav" aria-label="Panel administrativo">
    <?php foreach ($adminLinks as [$path, $label]): ?>
        <a class="<?= $currentPath === $path ? 'is-active' : '' ?>" href="<?= $e($app['base_url'] . $path) ?>"><?= $e($label) ?></a>
    <?php endforeach; ?>
    <a href="<?= $e($app['base_url']) ?>/">Sitio público</a>
</nav>
