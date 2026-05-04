<?php

namespace App\Models;

use App\Core\Model;

class PasswordReset extends Model
{
    protected string $table = 'password_resets';

    public function create(string $email, string $token): void
    {
        $statement = $this->db->prepare('INSERT INTO password_resets (email, token, created_at) VALUES (:email, :token, NOW())');
        $statement->execute(compact('email', 'token'));
    }

    public function findByToken(string $token): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM password_resets WHERE token = :token ORDER BY id DESC LIMIT 1');
        $statement->execute(['token' => $token]);
        $record = $statement->fetch();
        return $record ?: null;
    }

    public function deleteByEmail(string $email): void
    {
        $statement = $this->db->prepare('DELETE FROM password_resets WHERE email = :email');
        $statement->execute(['email' => $email]);
    }
}
