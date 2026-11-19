<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DateTimeImmutable;
use PDO;
use RuntimeException;

final class Reservation
{
    public const STATUSES = [
        'pending_payment' => 'Pendiente de pago',
        'payment_submitted' => 'Comprobante enviado',
        'confirmed' => 'Confirmada',
        'rejected' => 'Rechazada',
        'cancelled' => 'Cancelada',
        'expired' => 'Expirada',
    ];

    public const PAYMENT_METHODS = [
        'transfer' => 'Transferencia',
        'cash_deposit' => 'Depósito en efectivo',
        'oxxo' => 'OXXO',
        'other' => 'Otro',
    ];

    private const ACTIVE_STATUSES = ['pending_payment', 'payment_submitted', 'confirmed'];

    public function all(?int $accommodationId = null, ?string $status = null): array
    {
        $sql = 'SELECT r.*, a.name AS accommodation_name, a.accommodation_type, a.neighborhood, a.cover_image,
                       u.full_name AS user_name, u.email AS user_email,
                       (SELECT COUNT(*) FROM payment_receipts p WHERE p.reservation_id = r.id AND p.review_status = \'pending\') AS pending_receipts,
                       (SELECT COUNT(*) FROM events e WHERE e.reservation_id = r.id) AS event_count,
                       (SELECT COUNT(*) FROM transports t WHERE t.reservation_id = r.id) AS transport_count
                FROM reservations r
                JOIN accommodations a ON a.id = r.accommodation_id
                LEFT JOIN users u ON u.id = r.user_id
                WHERE 1 = 1';
        $params = [];
        if ($accommodationId !== null && $accommodationId > 0) {
            $sql .= ' AND r.accommodation_id = ?';
            $params[] = $accommodationId;
        }
        if ($status !== null && isset(self::STATUSES[$status])) {
            $sql .= ' AND r.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY r.created_at DESC, r.id DESC';
        $query = Database::connection()->prepare($sql);
        $query->execute($params);
        return $query->fetchAll();
    }

    public function find(int $id): ?array
    {
        $query = Database::connection()->prepare($this->baseSelect() . ' WHERE r.id = ?');
        $query->execute([$id]);
        $reservation = $query->fetch();
        return $reservation ? $this->withRelations($reservation) : null;
    }

    public function findByFolio(string $folio): ?array
    {
        $query = Database::connection()->prepare($this->baseSelect() . ' WHERE r.folio = ?');
        $query->execute([$folio]);
        $reservation = $query->fetch();
        return $reservation ? $this->withRelations($reservation) : null;
    }

    public function forUser(int $userId): array
    {
        $query = Database::connection()->prepare($this->baseSelect() . ' WHERE r.user_id = ? ORDER BY r.created_at DESC, r.id DESC');
        $query->execute([$userId]);
        return $query->fetchAll();
    }

    public function create(
        int $accommodationId,
        ?int $userId,
        string $guestName,
        string $guestEmail,
        string $guestPhone,
        int $guests,
        string $checkIn,
        string $checkOut,
        string $notes
    ): array {
        $db = Database::connection();
        $query = $db->prepare('SELECT * FROM accommodations WHERE id = ? AND is_active = 1 AND is_published = 1');
        $query->execute([$accommodationId]);
        $stay = $query->fetch();
        if (!$stay) {
            throw new RuntimeException('El alojamiento no está disponible para reservar.');
        }

        $in = DateTimeImmutable::createFromFormat('!Y-m-d', $checkIn);
        $out = DateTimeImmutable::createFromFormat('!Y-m-d', $checkOut);
        $today = new DateTimeImmutable('today');
        if (!$in || !$out || $out <= $in) {
            throw new RuntimeException('Revisa las fechas: la salida debe ser posterior a la entrada.');
        }
        if ($in < $today) {
            throw new RuntimeException('La fecha de entrada no puede estar en el pasado.');
        }
        if ($guests < 1 || $guests > (int) $stay['capacity']) {
            throw new RuntimeException('El número de huéspedes excede la capacidad del alojamiento.');
        }

        $nights = (int) $in->diff($out)->days;
        $nightly = (float) $stay['nightly_rate'];
        $subtotal = round($nightly * $nights, 2);
        $cleaning = round((float) $stay['cleaning_fee'], 2);
        $tax = round($subtotal * 0.19, 2);
        $total = round($subtotal + $cleaning + $tax, 2);
        $app = require dirname(__DIR__, 2) . '/config/app.php';
        $holdDays = max(1, (int) ($app['reservation_hold_days'] ?? 3));
        $holdUntil = (new DateTimeImmutable('now'))->modify('+' . $holdDays . ' days')->format('Y-m-d H:i:s');

        $db->beginTransaction();
        try {
            $overlap = $db->prepare(
                "SELECT id FROM reservations
                 WHERE accommodation_id = ? AND status IN ('pending_payment', 'payment_submitted', 'confirmed')
                 AND check_in < ? AND check_out > ?
                 AND (status <> 'pending_payment' OR hold_expires_at > NOW())
                 FOR UPDATE"
            );
            $overlap->execute([$accommodationId, $checkOut, $checkIn]);
            if ($overlap->fetch()) {
                $db->rollBack();
                throw new RuntimeException('Esas fechas ya no están disponibles. Prueba con otro periodo.');
            }

            $folio = $this->generateFolio($db);
            $insert = $db->prepare(
                'INSERT INTO reservations
                    (folio, accommodation_id, user_id, guest_name, guest_email, guest_phone, check_in, check_out,
                     guest_count, status, nightly_subtotal, cleaning_fee, tax_amount, deposit_amount, total_amount,
                     hold_expires_at, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?)'
            );
            $insert->execute([
                $folio, $accommodationId, $userId, $guestName, $guestEmail, $guestPhone, $checkIn, $checkOut,
                $guests, 'pending_payment', $subtotal, $cleaning, $tax, $total, $holdUntil, $notes === '' ? null : $notes,
            ]);
            $id = (int) $db->lastInsertId();
            $db->commit();
            return ['id' => $id, 'folio' => $folio];
        } catch (RuntimeException $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }
    }

    public function confirm(int $id): bool
    {
        $statement = Database::connection()->prepare(
            "UPDATE reservations SET status = 'confirmed', confirmed_at = NOW()
             WHERE id = ? AND status IN ('pending_payment', 'payment_submitted')"
        );
        $statement->execute([$id]);
        return $statement->rowCount() > 0;
    }

    public function cancel(int $id): bool
    {
        $statement = Database::connection()->prepare(
            "UPDATE reservations SET status = 'cancelled'
             WHERE id = ? AND status IN ('pending_payment', 'payment_submitted', 'confirmed', 'rejected')"
        );
        $statement->execute([$id]);
        return $statement->rowCount() > 0;
    }

    public function markRejected(int $id): bool
    {
        $statement = Database::connection()->prepare(
            "UPDATE reservations SET status = 'rejected'
             WHERE id = ? AND status IN ('pending_payment', 'payment_submitted')"
        );
        $statement->execute([$id]);
        return $statement->rowCount() > 0;
    }

    public static function label(string $status): string
    {
        return self::STATUSES[$status] ?? $status;
    }

    public static function isActive(string $status): bool
    {
        return in_array($status, self::ACTIVE_STATUSES, true);
    }

    private function baseSelect(): string
    {
        return 'SELECT r.*, a.name AS accommodation_name, a.accommodation_type, a.neighborhood, a.cover_image,
                       u.full_name AS user_name, u.email AS user_email
                FROM reservations r
                JOIN accommodations a ON a.id = r.accommodation_id
                LEFT JOIN users u ON u.id = r.user_id';
    }

    private function withRelations(array $reservation): array
    {
        $db = Database::connection();
        $receipts = $db->prepare('SELECT * FROM payment_receipts WHERE reservation_id = ? ORDER BY created_at DESC, id DESC');
        $receipts->execute([$reservation['id']]);
        $reservation['receipts'] = $receipts->fetchAll();

        $events = $db->prepare('SELECT e.*, u.full_name AS guest_name_text FROM events e LEFT JOIN users u ON u.id = e.user_id
                                WHERE e.reservation_id = ? ORDER BY e.event_date, e.id');
        $events->execute([$reservation['id']]);
        $reservation['events'] = $events->fetchAll();

        $transports = $db->prepare('SELECT * FROM transports WHERE reservation_id = ? ORDER BY pickup_date, id');
        $transports->execute([$reservation['id']]);
        $reservation['transports'] = $transports->fetchAll();

        return $reservation;
    }

    private function generateFolio(PDO $db): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $folio = 'HT-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $check = $db->prepare('SELECT 1 FROM reservations WHERE folio = ?');
            $check->execute([$folio]);
            if (!$check->fetchColumn()) {
                return $folio;
            }
        }
        throw new RuntimeException('No se pudo generar el folio de la reservación.');
    }
}
