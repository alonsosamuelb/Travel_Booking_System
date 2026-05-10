<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function findActiveById(int $id): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function findByApiToken(string $token): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM {$this->table} WHERE api_token = :api_token AND deleted_at IS NULL LIMIT 1");
        $statement->execute(['api_token' => hash('sha256', $token)]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function paginate(array $filters, int $page = 1, int $perPage = 10): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(full_name LIKE :search OR email LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['role'])) {
            $where[] = 'role = :role';
            $params['role'] = $filters['role'];
        }

        $countSql = 'SELECT COUNT(*) FROM users WHERE ' . implode(' AND ', $where);
        $countStatement = $this->db->prepare($countSql);
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $sql = 'SELECT * FROM users WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $statement = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return ['data' => $statement->fetchAll(), 'total' => $total, 'per_page' => $perPage, 'page' => $page];
    }

    public function create(array $data): int
    {
        $statement = $this->db->prepare('
            INSERT INTO users (full_name, email, password, phone, role, deleted_at, created_at, updated_at)
            VALUES (:full_name, :email, :password, :phone, :role, :deleted_at, NOW(), NOW())
        ');
        $statement->execute([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? '',
            'role' => $data['role'] ?? 'user',
            'deleted_at' => $data['deleted_at'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, array $data): void
    {
        $statement = $this->db->prepare('
            UPDATE users
            SET full_name = :full_name, email = :email, phone = :phone, updated_at = NOW()
            WHERE id = :id
        ');
        $statement->execute($data + ['id' => $id]);
    }

    public function updatePassword(int $id, string $password): void
    {
        $statement = $this->db->prepare('UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id');
        $statement->execute(['id' => $id, 'password' => $password]);
    }

    public function softDelete(int $id): void
    {
        $statement = $this->db->prepare('UPDATE users SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function reactivate(string $email): void
    {
        $statement = $this->db->prepare('UPDATE users SET deleted_at = NULL, updated_at = NOW() WHERE email = :email');
        $statement->execute(['email' => $email]);
    }

    public function reactivateById(int $id): void
    {
        $statement = $this->db->prepare('UPDATE users SET deleted_at = NULL, updated_at = NOW() WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function saveByAdmin(?int $id, array $data): void
    {
        if ($id) {
            $fields = 'full_name = :full_name, email = :email, phone = :phone, role = :role, deleted_at = :deleted_at, updated_at = NOW()';
            $params = $data + ['id' => $id];

            if (!empty($data['password'])) {
                $fields .= ', password = :password';
                $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                unset($params['password']);
            }

            $statement = $this->db->prepare("UPDATE users SET {$fields} WHERE id = :id");
            $statement->execute($params);
            return;
        }

        $this->create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'phone' => $data['phone'] ?? '',
            'role' => $data['role'] ?? 'user',
            'deleted_at' => $data['deleted_at'] ?? null,
        ]);
    }

    public function topBookers(): array
    {
        $statement = $this->db->query('
            SELECT u.full_name, u.email, COUNT(r.id) AS total_reservations
            FROM users u
            LEFT JOIN reservations r ON r.user_id = u.id AND r.status = "active"
            GROUP BY u.id
            ORDER BY total_reservations DESC, u.full_name ASC
            LIMIT 5
        ');

        return $statement->fetchAll();
    }

    public function refreshApiToken(int $id): string
    {
        $plainToken = bin2hex(random_bytes(32));
        $statement = $this->db->prepare('UPDATE users SET api_token = :api_token, updated_at = NOW() WHERE id = :id');
        $statement->execute([
            'id' => $id,
            'api_token' => hash('sha256', $plainToken),
        ]);

        return $plainToken;
    }

    public function revokeApiToken(int $id): void
    {
        $statement = $this->db->prepare('UPDATE users SET api_token = NULL, updated_at = NOW() WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function countActiveAdmins(): int
    {
        $statement = $this->db->query('SELECT COUNT(*) FROM users WHERE role = "admin" AND deleted_at IS NULL');
        return (int) $statement->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user ?: null;
    }
}
