<?php

namespace App\Core;

class App
{
    private static array $config = [];

    public static function boot(): void
    {
        self::$config = [
            'app' => require __DIR__ . '/../../config/app.php',
            'database' => require __DIR__ . '/../../config/database.php',
        ];
    }

    public static function config(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = self::$config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public static function setConfig(string $key, mixed $value): void
    {
        self::$config[$key] = $value;
    }
}
