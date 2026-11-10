<?php $signedIn = isset($_SESSION['user_id']); ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $signedIn ? 'Mi cuenta' : ($register ? 'Crear cuenta' : 'Iniciar sesión') ?> | Huatulco</title>
    <link rel="stylesheet" href="<?= $e($app['base_url']) ?>/assets/css/app.css">
</head>
<body class="account-page">
<header class="site-header">
    <a class="brand" href="<?= $e($app['base_url']) ?>/" aria-label="Huatulco, inicio">
        <span class="brand-mark">H</span>
        <span>HUATULCO<small>ESTANCIAS &amp; EXPERIENCIAS</small></span>
    </a>
    <a class="account-link" href="<?= $e($app['base_url']) ?>/">← Volver al inicio</a>
</header>
<main class="account-wrapper">
    <section class="account-card" aria-labelledby="account-title">
        <p class="eyebrow">ACCESO A TU CUENTA</p>
        <?php if ($signedIn): ?>
            <h1 id="account-title">Mi cuenta</h1>
            <p class="account-intro">Consulta tus solicitudes de actividades en Huatulco.</p>
            <p><a class="account-link" href="<?= $e($app['base_url']) ?>/actividades#mis-solicitudes">Mis solicitudes</a></p>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <p><a class="account-link" href="<?= $e($app['base_url']) ?>/admin/alojamientos">Administrar alojamientos</a></p>
                <p><a class="account-link" href="<?= $e($app['base_url']) ?>/admin/actividades">Administrar actividades</a></p>
            <?php endif; ?>
            <form method="post" action="<?= $e($app['base_url']) ?>/cuenta/salir">
                <input type="hidden" name="token" value="<?= $e($token) ?>">
                <button class="button button-gold account-submit" type="submit">Cerrar sesión</button>
            </form>
        <?php else: ?>
            <h1 id="account-title"><?= $register ? 'Crear cuenta' : 'Bienvenido' ?></h1>
            <p class="account-intro"><?= $register
                ? 'Crea tu cuenta para solicitar actividades y consultar su disponibilidad.'
                : 'Inicia sesión para solicitar actividades y consultar el estado de tus solicitudes.' ?></p>
            <?php if ($error): ?><p class="account-error" role="alert"><?= $e($error) ?></p><?php endif; ?>
            <form method="post" class="travel-form account-form">
                <input type="hidden" name="token" value="<?= $e($token) ?>">
                <?php if ($register): ?>
                    <label>Nombre completo <input name="name" maxlength="160" autocomplete="name" required></label>
                <?php endif; ?>
                <label>Correo electrónico <input type="email" name="email" maxlength="190" autocomplete="username" placeholder="tu@correo.com" required></label>
                <label>Contraseña <input type="password" name="password" autocomplete="<?= $register ? 'new-password' : 'current-password' ?>" required></label>
                <button class="button button-gold account-submit" type="submit"><?= $register ? 'Crear cuenta' : 'Iniciar sesión' ?></button>
            </form>
            <p class="account-switch">
                <?= $register ? '¿Ya tienes cuenta?' : '¿Aún no tienes cuenta?' ?>
                <a class="account-link" href="<?= $e($app['base_url']) ?>/cuenta/<?= $register ? 'ingresar' : 'registro' ?>"><?= $register ? 'Inicia sesión' : 'Crea una cuenta' ?></a>
            </p>
        <?php endif; ?>
    </section>
</main>
<footer>
    <a class="brand" href="<?= $e($app['base_url']) ?>/">HUATULCO</a>
    <p>Bahías de Huatulco, Oaxaca · México</p>
</footer>
</body>
</html>
