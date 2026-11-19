<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use RuntimeException;

final class Payment
{
    public const STATUSES = [
        'pending' => 'En revisión',
        'approved' => 'Aprobado',
        'rejected' => 'Rechazado',
    ];

    public function pending(): array
    {
        $sql = "SELECT p.*, r.folio, r.guest_name, r.guest_email, r.guest_phone, r.check_in, r.check_out,
                       r.total_amount, r.status AS reservation_status,
                       a.name AS accommodation_name, u.full_name AS user_name
                FROM payment_receipts p
                JOIN reservations r ON r.id = p.reservation_id
                JOIN accommodations a ON a.id = r.accommodation_id
                LEFT JOIN users u ON u.id = r.user_id
                WHERE p.review_status = 'pending'
                ORDER BY p.created_at ASC, p.id ASC";
        return Database::connection()->query($sql)->fetchAll();
    }

    public function history(int $limit = 25): array
    {
        $sql = "SELECT p.*, r.folio, r.guest_name, a.name AS accommodation_name, u.full_name AS reviewer_name
                FROM payment_receipts p
                JOIN reservations r ON r.id = p.reservation_id
                JOIN accommodations a ON a.id = r.accommodation_id
                LEFT JOIN users u ON u.id = p.reviewed_by
                WHERE p.review_status <> 'pending'
                ORDER BY p.reviewed_at DESC, p.id DESC
                LIMIT " . max(1, min(100, $limit));
        return Database::connection()->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $query = Database::connection()->prepare(
            'SELECT p.*, r.folio, r.guest_name, r.guest_email, r.total_amount, r.status AS reservation_status,
                    a.name AS accommodation_name
             FROM payment_receipts p
             JOIN reservations r ON r.id = p.reservation_id
             JOIN accommodations a ON a.id = r.accommodation_id
             WHERE p.id = ?'
        );
        $query->execute([$id]);
        $receipt = $query->fetch();
        return $receipt ?: null;
    }

    public function create(
        int $reservationId,
        string $method,
        ?float $amount,
        string $storedPath,
        string $originalName,
        string $mimeType,
        int $sizeBytes
    ): void {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $query = $db->prepare('SELECT id, status, total_amount FROM reservations WHERE id = ? FOR UPDATE');
            $query->execute([$reservationId]);
            $reservation = $query->fetch();
            if (!$reservation) {
                throw new RuntimeException('La reservación no existe.');
            }
            if (!in_array($reservation['status'], ['pending_payment', 'payment_submitted', 'rejected'], true)) {
                throw new RuntimeException('Esta reservación ya no acepta comprobantes de pago.');
            }

            $insert = $db->prepare(
                'INSERT INTO payment_receipts
                    (reservation_id, payment_method, receipt_path, amount_reported, review_status,
                     original_name, mime_type, size_bytes)
                 VALUES (?, ?, ?, ?, \'pending\', ?, ?, ?)'
            );
            $insert->execute([
                $reservationId,
                $method,
                $storedPath,
                $amount ?? (float) $reservation['total_amount'],
                $originalName,
                $mimeType,
                $sizeBytes,
            ]);

            $db->prepare("UPDATE reservations SET status = 'payment_submitted', payment_submitted_at = NOW() WHERE id = ?")
                ->execute([$reservationId]);

            $db->commit();
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }
    }

    public function approve(int $receiptId, int $adminId): bool
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $query = $db->prepare('SELECT id, reservation_id FROM payment_receipts WHERE id = ? AND review_status = \'pending\' FOR UPDATE');
            $query->execute([$receiptId]);
            $receipt = $query->fetch();
            if (!$receipt) {
                $db->rollBack();
                return false;
            }

            $db->prepare("UPDATE payment_receipts
                          SET review_status = 'approved', reviewed_by = ?, reviewed_at = NOW()
                          WHERE id = ?")
                ->execute([$adminId, $receiptId]);

            $db->prepare("UPDATE reservations
                          SET status = 'confirmed', confirmed_at = NOW()
                          WHERE id = ? AND status IN ('pending_payment', 'payment_submitted')")
                ->execute([$receipt['reservation_id']]);

            $db->commit();
            return true;
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }
    }

    public function reject(int $receiptId, int $adminId): bool
    {
        $db = Database::connection();
        $db->beginTransaction();
        try {
            $query = $db->prepare('SELECT id, reservation_id FROM payment_receipts WHERE id = ? AND review_status = \'pending\' FOR UPDATE');
            $query->execute([$receiptId]);
            $receipt = $query->fetch();
            if (!$receipt) {
                $db->rollBack();
                return false;
            }

            $db->prepare("UPDATE payment_receipts
                          SET review_status = 'rejected', reviewed_by = ?, reviewed_at = NOW()
                          WHERE id = ?")
                ->execute([$adminId, $receiptId]);

            $db->prepare("UPDATE reservations
                          SET status = 'rejected'
                          WHERE id = ? AND status IN ('pending_payment', 'payment_submitted')")
                ->execute([$receipt['reservation_id']]);

            $db->commit();
            return true;
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }
    }

    public static function label(string $status): string
    {
        return self::STATUSES[$status] ?? $status;
    }
}
