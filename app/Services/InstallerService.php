<?php

namespace App\Services;

use App\Core\App;
use App\Core\Database;
use App\Core\Env;
use PDO;
use PDOException;

class InstallerService
{
    public function isInstalled(): bool
    {
        $envFile = $this->envPath();
        if (!is_file($envFile)) {
            return false;
        }

        try {
            $db = Database::makeConnection(App::config('database'));
            $statement = $db->query("SHOW TABLES LIKE 'schema_migrations'");
            return (bool) $statement->fetchColumn();
        } catch (\Throwable) {
            return false;
        }
    }

    public function install(array $data): array
    {
        $config = [
            'host' => trim((string) ($data['db_host'] ?? '127.0.0.1')),
            'port' => (int) ($data['db_port'] ?? 3306),
            'database' => trim((string) ($data['db_database'] ?? 'travel_booking_system')),
            'username' => trim((string) ($data['db_username'] ?? 'root')),
            'password' => (string) ($data['db_password'] ?? ''),
            'charset' => trim((string) ($data['db_charset'] ?? 'utf8mb4')),
        ];

        $this->assertConnection($config);
        $this->createDatabaseIfNeeded($config);
        $this->writeEnvFile($data, $config);

        Env::load($this->envPath());
        App::boot();
        Database::reset();

        $migrationService = new MigrationService();
        $migrations = $migrationService->migrate();
        $migrationService->seed();

        return $migrations;
    }

    public function defaults(): array
    {
        return [
            'app_name' => (string) Env::get('APP_NAME', 'Travel Booking System'),
            'app_env' => (string) Env::get('APP_ENV', 'local'),
            'app_debug' => Env::get('APP_DEBUG', true) ? 'true' : 'false',
            'app_timezone' => (string) Env::get('APP_TIMEZONE', 'Europe/Madrid'),
            'app_base_url' => (string) Env::get('APP_BASE_URL', '/Travel_Booking_System/public'),
            'support_email' => (string) Env::get('SUPPORT_EMAIL', 'support@travelbooking.local'),
            'db_host' => (string) Env::get('DB_HOST', '127.0.0.1'),
            'db_port' => (string) Env::get('DB_PORT', '3306'),
            'db_database' => (string) Env::get('DB_DATABASE', 'travel_booking_system'),
            'db_username' => (string) Env::get('DB_USERNAME', 'root'),
            'db_password' => (string) Env::get('DB_PASSWORD', ''),
            'db_charset' => (string) Env::get('DB_CHARSET', 'utf8mb4'),
            'reservation_limit_per_user' => (string) Env::get('RESERVATION_LIMIT_PER_USER', '3'),
        ];
    }

    private function assertConnection(array $config): void
    {
        try {
            Database::makeConnection($config, false);
        } catch (PDOException $exception) {
            throw new \RuntimeException('Database server connection failed: ' . $exception->getMessage());
        }
    }

    private function createDatabaseIfNeeded(array $config): void
    {
        $serverConnection = Database::makeConnection($config, false);
        $databaseName = str_replace('`', '``', $config['database']);
        $serverConnection->exec("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET {$config['charset']} COLLATE {$config['charset']}_unicode_ci");
    }

    private function writeEnvFile(array $data, array $config): void
    {
        $lines = [
            'APP_NAME="' . addslashes((string) ($data['app_name'] ?? 'Travel Booking System')) . '"',
            'APP_ENV=' . ($data['app_env'] ?? 'local'),
            'APP_DEBUG=' . ($data['app_debug'] ?? 'true'),
            'APP_TIMEZONE=' . ($data['app_timezone'] ?? 'Europe/Madrid'),
            'APP_BASE_URL=' . ($data['app_base_url'] ?? '/Travel_Booking_System/public'),
            'SUPPORT_EMAIL=' . ($data['support_email'] ?? 'support@travelbooking.local'),
            '',
            'DB_HOST=' . $config['host'],
            'DB_PORT=' . $config['port'],
            'DB_DATABASE=' . $config['database'],
            'DB_USERNAME=' . $config['username'],
            'DB_PASSWORD=' . $config['password'],
            'DB_CHARSET=' . $config['charset'],
            '',
            'RESERVATION_LIMIT_PER_USER=' . ($data['reservation_limit_per_user'] ?? '3'),
        ];

        file_put_contents($this->envPath(), implode(PHP_EOL, $lines) . PHP_EOL);
    }

    private function envPath(): string
    {
        return __DIR__ . '/../../.env';
    }
}
