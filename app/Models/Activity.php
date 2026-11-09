<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDOException;

final class Activity
{
    public const SECTIONS = [
        'acuatica' => 'Actividades acuáticas',
        'aventura' => 'Aventura en tierra',
        'aerea' => 'Actividades aéreas',
        'cultura' => 'Experiencias culturales y de relajación',
    ];

    public function published(): array
    {
        $sql = "SELECT id, name, category, description, price, duration_minutes, capacity,
                COALESCE(catalog_section, CASE WHEN category IN ('cultura', 'social') THEN 'cultura' ELSE 'aventura' END) AS catalog_section
                FROM activities WHERE is_published = 1 ORDER BY id";
        return Database::connection()->query($sql)->fetchAll();
    }

    public function requestsFor(int $userId): array
    {
        $query = Database::connection()->prepare(
            'SELECT a.name, r.requested_date, r.participants, r.status, r.response
             FROM activity_requests r JOIN activities a ON a.id = r.activity_id
             WHERE r.user_id = ? ORDER BY r.created_at DESC, r.id DESC'
        );
        $query->execute([$userId]);
        return $query->fetchAll();
    }

    public function request(int $activityId, int $userId, string $date, int $participants): string
    {
        $query = Database::connection()->prepare(
            'INSERT INTO activity_requests (activity_id, user_id, requested_date, participants)
             SELECT id, ?, ?, ? FROM activities WHERE id = ? AND is_published = 1
             AND (capacity IS NULL OR capacity >= ?)'
        );
        try {
            $query->execute([$userId, $date, $participants, $activityId, $participants]);
        } catch (PDOException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) !== 1062) {
                throw $exception;
            }
            return 'Ya tienes una solicitud para esa actividad y fecha.';
        }
        return $query->rowCount() === 1
            ? 'Solicitud enviada. Consulta aquí el estado después de que el administrador revise la disponibilidad.'
            : 'La actividad no está publicada o el grupo supera el máximo permitido.';
    }
}
