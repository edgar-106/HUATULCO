<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Accommodation
{
    public function featured(): array
    {
        return $this->allPublished(3);
    }

    public function availableForCatalog(): array
    {
        return $this->allPublished();
    }

    private function allPublished(?int $limit = null): array
    {
        $sql = 'SELECT id, name, accommodation_type, description, nightly_rate, capacity, bedrooms, cover_image, neighborhood, latitude, longitude FROM accommodations WHERE is_active = 1 AND is_published = 1 ORDER BY is_featured DESC, name ASC';
        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int) $limit;
        }

        return Database::connection()->query($sql)->fetchAll();
    }

    public function withCoordinates(): array
    {
        $sql = 'SELECT id, name, accommodation_type, neighborhood, nightly_rate, latitude, longitude FROM accommodations WHERE is_active = 1 AND is_published = 1 AND latitude IS NOT NULL AND longitude IS NOT NULL ORDER BY name';
        return Database::connection()->query($sql)->fetchAll();
    }
}
