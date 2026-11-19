<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$configPath = $root . '/config/database.php';

try {
    if (!is_file($configPath) && !copy($root . '/config/database.example.php', $configPath)) {
        throw new RuntimeException('No se pudo crear config/database.php.');
    }

    $config = require $configPath;
    $database = $config['database'];
    if (!is_string($database) || !preg_match('/^[a-zA-Z0-9_]{1,64}$/D', $database)) {
        throw new RuntimeException('El nombre de la base debe contener solo letras, números y guiones bajos.');
    }
    $dsn = sprintf('mysql:host=%s;port=%d;charset=%s', $config['host'], $config['port'], $config['charset']);
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $query = $pdo->prepare("SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'accommodations'");
    $query->execute([$database]);
    $fresh = !$query->fetchColumn();
    $query->closeCursor();

    $files = $fresh ? ['schema.sql', 'demo.sql', 'catalog.sql'] : ['schema.sql', 'catalog.sql'];
    foreach ($files as $file) {
        $sql = file_get_contents($root . '/database/' . $file);
        if ($sql === false) {
            throw new RuntimeException("No se pudo leer $file.");
        }
        $sql = str_replace(['EXISTS huatulco CHARACTER SET', 'USE huatulco;'], ["EXISTS `$database` CHARACTER SET", "USE `$database`;"], $sql);
        $result = $pdo->query($sql);
        // MariaDB returns later statement errors while advancing through the batch.
        while ($result->nextRowset()) {
        }
        $result->closeCursor();
    }
    echo "Base de datos lista.\n";
} catch (PDOException $exception) {
    $code = (int) ($exception->errorInfo[1] ?? 0);
    if (in_array($code, [1044, 1045, 2002], true)) {
        fwrite(STDERR, "Inicia MySQL en XAMPP y revisa los datos de conexión y permisos en config/database.php.\n");
    } else {
        fwrite(STDERR, "No se pudo actualizar la base. Código de MariaDB: $code.\n");
    }
    exit(1);
} catch (RuntimeException $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
