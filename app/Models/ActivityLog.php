<?php

namespace App\Models;

use App\Core\Model;

class ActivityLog extends Model
{
    protected string $table = 'activity_logs';

    public function create(array $data): void
    {
        $statement = $this->db->prepare('
            INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at)
            VALUES (:user_id, :action, :entity_type, :entity_id, :description, :ip_address, NOW())
        ');
        $statement->execute($data);
    }

    public function latest(int $limit = 15): array
    {
        $statement = $this->db->prepare('
            SELECT a.*, u.full_name, u.email
            FROM activity_logs a
            LEFT JOIN users u ON u.id = a.user_id
            ORDER BY a.created_at DESC, a.id DESC
            LIMIT :limit
        ');
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
