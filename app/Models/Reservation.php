<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Reservation extends Model
{
    protected string $table = 'reservations';

    public function paginate(array $filters, int $page = 1, int $perPage = 10, ?int $userId = null): array
    {
        $this->syncFinishedReservations();

        $where = ['1=1'];
        $params = [];

        if ($userId !== null) {
            $where[] = 'r.user_id = :user_id';
            $params['user_id'] = $userId;
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $where[] = 'r.status = "active" AND t.departure_at >= NOW()';
            } elseif ($filters['status'] === 'finished') {
                $where[] = 'r.status = "completed"';
            } else {
                $where[] = 'r.status = :status';
                $params['status'] = $filters['status'];
            }
        } elseif ($userId !== null) {
            $where[] = 'r.status != "completed"';
        }

        if (!empty($filters['search'])) {
            $where[] = '(u.full_name LIKE :search OR t.name LIKE :search OR t.destination LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $countStatement = $this->db->prepare('
            SELECT COUNT(*)
            FROM reservations r
            INNER JOIN users u ON u.id = r.user_id
            INNER JOIN trips t ON t.id = r.trip_id
            WHERE ' . implode(' AND ', $where)
        );
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $statement = $this->db->prepare('
            SELECT r.*, u.full_name, u.email, t.name AS trip_name, t.origin, t.destination, t.departure_at
            FROM reservations r
            INNER JOIN users u ON u.id = r.user_id
            INNER JOIN trips t ON t.id = r.trip_id
            WHERE ' . implode(' AND ', $where) . '
            ORDER BY t.departure_at DESC
            LIMIT :limit OFFSET :offset
        ');
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return ['data' => $statement->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'page' => $page];
    }

    public function findDetailed(int $id): ?array
    {
        $this->syncFinishedReservations();

        $statement = $this->db->prepare('
            SELECT r.*, u.full_name, u.email, t.name AS trip_name, t.origin, t.destination, t.departure_at, t.vehicle
            FROM reservations r
            INNER JOIN users u ON u.id = r.user_id
            INNER JOIN trips t ON t.id = r.trip_id
            WHERE r.id = :id
            LIMIT 1
        ');
        $statement->execute(['id' => $id]);
        $reservation = $statement->fetch();
        return $reservation ?: null;
    }

    public function activeSeatsForTrip(int $tripId): int
    {
        $this->syncFinishedReservations();

        $statement = $this->db->prepare('SELECT COALESCE(SUM(seats_reserved), 0) FROM reservations WHERE trip_id = :trip_id AND status = "active"');
        $statement->execute(['trip_id' => $tripId]);
        return (int) $statement->fetchColumn();
    }

    public function userActiveReservationCount(int $userId): int
    {
        $this->syncFinishedReservations();

        $statement = $this->db->prepare('SELECT COUNT(*) FROM reservations WHERE user_id = :user_id AND status = "active"');
        $statement->execute(['user_id' => $userId]);
        return (int) $statement->fetchColumn();
    }

    public function hasUserTripConflict(int $userId, string $departureAt, ?int $ignoreReservationId = null): bool
    {
        $this->syncFinishedReservations();

        $sql = '
            SELECT COUNT(*)
            FROM reservations r
            INNER JOIN trips t ON t.id = r.trip_id
            WHERE r.user_id = :user_id
              AND r.status = "active"
              AND t.departure_at = :departure_at
        ';
        $params = ['user_id' => $userId, 'departure_at' => $departureAt];

        if ($ignoreReservationId) {
            $sql .= ' AND r.id != :reservation_id';
            $params['reservation_id'] = $ignoreReservationId;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn() > 0;
    }

    public function hasDuplicate(int $userId, int $tripId, ?int $ignoreReservationId = null): bool
    {
        $this->syncFinishedReservations();

        $sql = 'SELECT COUNT(*) FROM reservations WHERE user_id = :user_id AND trip_id = :trip_id AND status = "active"';
        $params = ['user_id' => $userId, 'trip_id' => $tripId];

        if ($ignoreReservationId) {
            $sql .= ' AND id != :reservation_id';
            $params['reservation_id'] = $ignoreReservationId;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn() > 0;
    }

    public function save(?int $id, array $data): int
    {
        if ($id) {
            $statement = $this->db->prepare('
                UPDATE reservations
                SET trip_id = :trip_id, reservation_date = :reservation_date, seats_reserved = :seats_reserved,
                    travel_role = :travel_role, notes = :notes, status = :status, updated_at = NOW()
                WHERE id = :id
            ');
            $statement->execute($data + ['id' => $id]);
            return $id;
        }

        $statement = $this->db->prepare('
            INSERT INTO reservations (user_id, trip_id, reservation_date, seats_reserved, travel_role, notes, status, created_at, updated_at)
            VALUES (:user_id, :trip_id, :reservation_date, :seats_reserved, :travel_role, :notes, :status, NOW(), NOW())
        ');
        $statement->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function cancel(int $id): void
    {
        $statement = $this->db->prepare('UPDATE reservations SET status = "cancelled", updated_at = NOW() WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function history(): array
    {
        $this->syncFinishedReservations();

        $statement = $this->db->query('
            SELECT DATE(reservation_date) AS reservation_day, COUNT(*) AS total
            FROM reservations
            GROUP BY DATE(reservation_date)
            ORDER BY reservation_day DESC
            LIMIT 15
        ');
        return $statement->fetchAll();
    }

    private function syncFinishedReservations(): void
    {
        $statement = $this->db->prepare('
            UPDATE reservations r
            INNER JOIN trips t ON t.id = r.trip_id
            SET r.status = "completed", r.updated_at = NOW()
            WHERE r.status = "active"
              AND t.departure_at < NOW()
        ');
        $statement->execute();
    }
}
