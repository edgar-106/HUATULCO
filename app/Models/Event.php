<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Event
{
    public const TYPES = [
        'cultural' => 'Cultural',
        'deportivo' => 'Deportivo',
        'social' => 'Social',
    ];

    public const STATUSES = [
        'scheduled' => 'Programada',
        'confirmed' => 'Confirmada',
        'in_progress' => 'En curso',
        'completed' => 'Completada',
        'cancelled' => 'Cancelada',
    ];

    public function all(?int $accommodationId = null, ?string $type = null, ?bool $sporadic = null, ?string $status = null): array
    {
        $sql = 'SELECT e.*, a.name AS accommodation_name, u.full_name AS user_name, u.email AS user_email,
                       r.folio AS reservation_folio
                FROM events e
                JOIN accommodations a ON a.id = e.accommodation_id
                LEFT JOIN users u ON u.id = e.user_id
                LEFT JOIN reservations r ON r.id = e.reservation_id
                WHERE 1 = 1';
        $params = [];
        if ($accommodationId !== null && $accommodationId > 0) {
            $sql .= ' AND e.accommodation_id = ?';
            $params[] = $accommodationId;
        }
        if ($type !== null && isset(self::TYPES[$type])) {
            $sql .= ' AND e.event_type = ?';
            $params[] = $type;
        }
        if ($sporadic !== null) {
            $sql .= ' AND e.is_sporadic = ?';
            $params[] = $sporadic ? 1 : 0;
        }
        if ($status !== null && isset(self::STATUSES[$status])) {
            $sql .= ' AND e.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY e.event_date ASC, e.id DESC';
        $query = Database::connection()->prepare($sql);
        $query->execute($params);
        return $query->fetchAll();
    }

    public function find(int $id): ?array
    {
        $query = Database::connection()->prepare(
            'SELECT e.*, a.name AS accommodation_name
             FROM events e JOIN accommodations a ON a.id = e.accommodation_id
             WHERE e.id = ?'
        );
        $query->execute([$id]);
        $event = $query->fetch();
        return $event ?: null;
    }

    public function save(array $data): int
    {
        $db = Database::connection();
        $id = (int) ($data['id'] ?? 0);
        $values = [
            (int) $data['accommodation_id'],
            $data['user_id'] !== null && (int) $data['user_id'] > 0 ? (int) $data['user_id'] : null,
            $data['reservation_id'] !== null && (int) $data['reservation_id'] > 0 ? (int) $data['reservation_id'] : null,
            trim((string) $data['name']),
            (string) $data['event_type'],
            !empty($data['is_sporadic']) ? 1 : 0,
            (string) $data['event_date'],
            trim((string) ($data['description'] ?? '')),
            trim((string) ($data['attraction_center'] ?? '')) ?: null,
            trim((string) ($data['provider'] ?? '')) ?: null,
            (string) $data['status'],
        ];

        if ($id > 0) {
            $db->prepare(
                'UPDATE events SET accommodation_id = ?, user_id = ?, reservation_id = ?, name = ?, event_type = ?,
                    is_sporadic = ?, event_date = ?, description = ?, attraction_center = ?, provider = ?, status = ?
                 WHERE id = ?'
            )->execute([...$values, $id]);
            return $id;
        }

        $db->prepare(
            'INSERT INTO events
                (accommodation_id, user_id, reservation_id, name, event_type, is_sporadic, event_date,
                 description, attraction_center, provider, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute($values);
        return (int) $db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $statement = Database::connection()->prepare('DELETE FROM events WHERE id = ?');
        $statement->execute([$id]);
        return $statement->rowCount() > 0;
    }

    public static function typeLabel(string $type): string
    {
        return self::TYPES[$type] ?? $type;
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUSES[$status] ?? $status;
    }
}
