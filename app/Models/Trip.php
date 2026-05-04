<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Trip extends Model
{
    protected string $table = 'trips';

    public function paginate(array $filters, int $page = 1, int $perPage = 6, bool $admin = false): array
    {
        $where = [$admin ? '1=1' : 't.status = "published"'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(t.name LIKE :search OR t.origin LIKE :search OR t.destination LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['origin'])) {
            $where[] = 't.origin LIKE :origin';
            $params['origin'] = '%' . $filters['origin'] . '%';
        }

        if (!empty($filters['destination'])) {
            $where[] = 't.destination LIKE :destination';
            $params['destination'] = '%' . $filters['destination'] . '%';
        }

        if (!empty($filters['upcoming'])) {
            $where[] = 't.departure_at >= NOW()';
        }

        $countStatement = $this->db->prepare('SELECT COUNT(*) FROM trips t WHERE ' . implode(' AND ', $where));
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $statement = $this->db->prepare('
            SELECT t.*, COALESCE(SUM(CASE WHEN r.status = "active" THEN r.seats_reserved ELSE 0 END), 0) AS reserved_seats
            FROM trips t
            LEFT JOIN reservations r ON r.trip_id = t.id
            WHERE ' . implode(' AND ', $where) . '
            GROUP BY t.id
            ORDER BY t.departure_at ASC
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

    public function allForSelect(): array
    {
        $statement = $this->db->query('SELECT id, name, departure_at FROM trips WHERE status = "published" AND departure_at >= NOW() ORDER BY departure_at ASC');
        return $statement->fetchAll();
    }

    public function findWithAvailability(int $id): ?array
    {
        $statement = $this->db->prepare('
            SELECT t.*, COALESCE(SUM(CASE WHEN r.status = "active" THEN r.seats_reserved ELSE 0 END), 0) AS reserved_seats
            FROM trips t
            LEFT JOIN reservations r ON r.trip_id = t.id
            WHERE t.id = :id
            GROUP BY t.id
            LIMIT 1
        ');
        $statement->execute(['id' => $id]);
        $trip = $statement->fetch();
        return $trip ?: null;
    }

    public function save(?int $id, array $data): void
    {
        if ($id) {
            $statement = $this->db->prepare('
                UPDATE trips
                SET name = :name, description = :description, origin = :origin, destination = :destination,
                    departure_at = :departure_at, vehicle = :vehicle, available_seats = :available_seats,
                    image_path = :image_path, status = :status, updated_at = NOW()
                WHERE id = :id
            ');
            $statement->execute($data + ['id' => $id]);
            return;
        }

        $statement = $this->db->prepare('
            INSERT INTO trips (name, description, origin, destination, departure_at, vehicle, available_seats, image_path, status, created_at, updated_at)
            VALUES (:name, :description, :origin, :destination, :departure_at, :vehicle, :available_seats, :image_path, :status, NOW(), NOW())
        ');
        $statement->execute($data);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM trips WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function mostBooked(): array
    {
        $statement = $this->db->query('
            SELECT t.name, t.origin, t.destination, COUNT(r.id) AS total_reservations
            FROM trips t
            LEFT JOIN reservations r ON r.trip_id = t.id AND r.status = "active"
            GROUP BY t.id
            ORDER BY total_reservations DESC, t.departure_at ASC
            LIMIT 5
        ');
        return $statement->fetchAll();
    }
}
