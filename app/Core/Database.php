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
            exit('Database connection error: ' . $exception->getMessage());
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

        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public static function reset(): void
    {
        self::$connection = null;
    }
}
