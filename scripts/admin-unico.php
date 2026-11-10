<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/Core/Database.php';

use App\Core\Database;

$email = trim($argv[1] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Indica el correo de una cuenta registrada.\n");
    exit(1);
}

try {
    $db = Database::connection();
    $db->beginTransaction();
    $query = $db->prepare('SELECT id FROM users WHERE email = ? FOR UPDATE');
    $query->execute([$email]);
    $id = $query->fetchColumn();
    if (!$id) {
        $db->rollBack();
        fwrite(STDERR, "Primero registra una cuenta con ese correo.\n");
        exit(1);
    }
    $db->prepare("UPDATE users SET role = CASE WHEN id = ? THEN 'admin' ELSE 'guest' END WHERE id = ? OR role = 'admin'")
        ->execute([$id, $id]);
    $db->commit();
    echo "La cuenta indicada es ahora la única administradora.\n";
} catch (PDOException) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    fwrite(STDERR, "No se pudo actualizar la cuenta administradora. Revisa MariaDB y vuelve a intentar.\n");
    exit(1);
}
