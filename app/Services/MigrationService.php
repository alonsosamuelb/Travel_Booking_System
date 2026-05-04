<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class MigrationService
{
    private PDO $db;
    private string $migrationPath;

    public function __construct(?string $migrationPath = null)
    {
        $this->db = Database::connection();
        $this->migrationPath = $migrationPath ?? __DIR__ . '/../../database/migrations';
    }

    public function migrate(): array
    {
        $this->ensureMigrationsTable();
        $applied = $this->appliedMigrations();
        $executed = [];

        foreach ($this->migrationFiles() as $file) {
            $name = basename($file);
            if (in_array($name, $applied, true)) {
                continue;
            }

            $sql = trim((string) file_get_contents($file));
            if ($sql !== '') {
                $this->db->exec($sql);
            }

            $statement = $this->db->prepare('INSERT INTO schema_migrations (migration, created_at) VALUES (:migration, NOW())');
            $statement->execute(['migration' => $name]);
            $executed[] = $name;
        }

        return $executed;
    }

    public function seed(): void
    {
        $adminHash = password_hash('Admin123!', PASSWORD_DEFAULT);
        $userHash = password_hash('User123!', PASSWORD_DEFAULT);

        $userCount = (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($userCount === 0) {
            $statement = $this->db->prepare('
                INSERT INTO users (full_name, email, password, phone, role, deleted_at, created_at, updated_at)
                VALUES
                (:admin_name, :admin_email, :admin_password, :admin_phone, "admin", NULL, NOW(), NOW()),
                (:user_name, :user_email, :user_password, :user_phone, "user", NULL, NOW(), NOW())
            ');
            $statement->execute([
                'admin_name' => 'System Admin',
                'admin_email' => 'admin@travelbooking.local',
                'admin_password' => $adminHash,
                'admin_phone' => '+34 600 111 222',
                'user_name' => 'Demo User',
                'user_email' => 'user@travelbooking.local',
                'user_password' => $userHash,
                'user_phone' => '+34 600 333 444',
            ]);
        }

        $tripCount = (int) $this->db->query('SELECT COUNT(*) FROM trips')->fetchColumn();
        if ($tripCount === 0) {
            $this->db->exec('
                INSERT INTO trips (name, description, origin, destination, departure_at, vehicle, available_seats, image_path, status, created_at, updated_at) VALUES
                ("Madrid to Valencia Weekend Ride", "Shared intercity trip with luggage space and direct route.", "Madrid", "Valencia", DATE_ADD(NOW(), INTERVAL 3 DAY), "Toyota Corolla", 4, "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80", "published", NOW(), NOW()),
                ("Barcelona Coastal Escape", "Morning departure with scenic route and one stop.", "Barcelona", "Tarragona", DATE_ADD(NOW(), INTERVAL 5 DAY), "Seat Leon", 3, "https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=900&q=80", "published", NOW(), NOW()),
                ("Seville to Granada Student Trip", "Affordable trip focused on students and light luggage.", "Seville", "Granada", DATE_ADD(NOW(), INTERVAL 7 DAY), "Renault Clio", 4, "https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=900&q=80", "published", NOW(), NOW())
            ');
        }

        $reservationCount = (int) $this->db->query('SELECT COUNT(*) FROM reservations')->fetchColumn();
        if ($reservationCount === 0) {
            $this->db->exec('
                INSERT INTO reservations (user_id, trip_id, reservation_date, seats_reserved, travel_role, notes, status, created_at, updated_at) VALUES
                (2, 1, NOW(), 1, "passenger", "Window seat preferred", "active", NOW(), NOW()),
                (2, 2, NOW(), 1, "driver", "Will bring small luggage", "active", NOW(), NOW())
            ');
        }
    }

    private function ensureMigrationsTable(): void
    {
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS schema_migrations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(190) NOT NULL UNIQUE,
                created_at DATETIME NOT NULL
            )
        ');
    }

    private function appliedMigrations(): array
    {
        $statement = $this->db->query('SELECT migration FROM schema_migrations ORDER BY migration ASC');
        return array_map(static fn (array $row) => $row['migration'], $statement->fetchAll());
    }

    private function migrationFiles(): array
    {
        $files = glob($this->migrationPath . '/*.sql') ?: [];
        sort($files);
        return $files;
    }
}
