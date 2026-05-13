<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        try {
            self::$connection = self::makeConnection(App::config('database'));
        } catch (PDOException $exception) {
            http_response_code(500);
            if (PHP_SAPI === 'cli') {
                exit('Database connection error: ' . $exception->getMessage());
            }

            exit('Database connection error. Please review the database configuration.');
        }

        return self::$connection;
    }

    public static function makeConnection(array $config, bool $withDatabase = true): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;%scharset=%s',
            $config['host'],
            $config['port'],
            $withDatabase ? 'dbname=' . $config['database'] . ';' : '',
            $config['charset']
        );

        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        self::applySessionTimezone($pdo);

        return $pdo;
    }

    private static function applySessionTimezone(PDO $pdo): void
    {
        $timezoneName = (string) App::config('app.timezone', 'UTC');

        try {
            $timezone = new \DateTimeZone($timezoneName);
            $offset = (new \DateTimeImmutable('now', $timezone))->format('P');
            $statement = $pdo->prepare('SET time_zone = :time_zone');
            $statement->execute(['time_zone' => $offset]);
        } catch (\Throwable) {
            // Shared hostings can restrict timezone handling. In that case we keep the default session timezone.
        }
    }

    public static function reset(): void
    {
        self::$connection = null;
    }
}
