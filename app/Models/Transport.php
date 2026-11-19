<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Transport
{
    public const STATUSES = [
        'assigned' => 'Asignado',
        'in_progress' => 'En curso',
        'completed' => 'Completado',
        'cancelled' => 'Cancelado',
    ];

    public function all(?int $reservationId = null): array
    {
        $sql = 'SELECT t.*, r.folio, r.guest_name, r.guest_phone, r.check_in, r.check_out,
                       a.name AS accommodation_name
                FROM transports t
                JOIN reservations r ON r.id = t.reservation_id
                JOIN accommodations a ON a.id = r.accommodation_id
                WHERE 1 = 1';
        $params = [];
        if ($reservationId !== null && $reservationId > 0) {
            $sql .= ' AND t.reservation_id = ?';
            $params[] = $reservationId;
        }
        $sql .= ' ORDER BY t.pickup_date ASC, t.id DESC';
        $query = Database::connection()->prepare($sql);
        $query->execute($params);
        return $query->fetchAll();
    }

    public function find(int $id): ?array
    {
        $query = Database::connection()->prepare(
            'SELECT t.*, r.folio, r.guest_name, a.name AS accommodation_name
             FROM transports t
             JOIN reservations r ON r.id = t.reservation_id
             JOIN accommodations a ON a.id = r.accommodation_id
             WHERE t.id = ?'
        );
        $query->execute([$id]);
        $transport = $query->fetch();
        return $transport ?: null;
    }

    public function save(array $data): int
    {
        $db = Database::connection();
        $id = (int) ($data['id'] ?? 0);
        $values = [
            (int) $data['reservation_id'],
            trim((string) $data['reason']),
            trim((string) $data['vehicle_type']),
            trim((string) ($data['plates'] ?? '')) ?: null,
            trim((string) ($data['driver'] ?? '')) ?: null,
            ($data['pickup_date'] ?? '') !== '' ? (string) $data['pickup_date'] : null,
            ($data['return_date'] ?? '') !== '' ? (string) $data['return_date'] : null,
            trim((string) ($data['notes'] ?? '')) ?: null,
            (string) $data['status'],
        ];

        if ($id > 0) {
            $db->prepare(
                'UPDATE transports SET reservation_id = ?, reason = ?, vehicle_type = ?, plates = ?, driver = ?,
                    pickup_date = ?, return_date = ?, notes = ?, status = ?
                 WHERE id = ?'
            )->execute([...$values, $id]);
            return $id;
        }

        $db->prepare(
            'INSERT INTO transports
                (reservation_id, reason, vehicle_type, plates, driver, pickup_date, return_date, notes, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute($values);
        return (int) $db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $statement = Database::connection()->prepare('DELETE FROM transports WHERE id = ?');
        $statement->execute([$id]);
        return $statement->rowCount() > 0;
    }

    public static function label(string $status): string
    {
        return self::STATUSES[$status] ?? $status;
    }
}
