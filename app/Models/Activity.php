<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Activity
{
    public function published(): array
    {
        $sql = 'SELECT id, name, category, description, price, duration_minutes FROM activities WHERE is_published = 1 ORDER BY category, name';
        return Database::connection()->query($sql)->fetchAll();
    }
}
