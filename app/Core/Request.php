<?php

namespace App\Core;

class Request
{
    private static ?array $jsonPayload = null;

    public static function method(): string
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if ($method === 'POST') {
            $override = $_POST['_method'] ?? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? null;
            if ($override) {
                $method = strtoupper((string) $override);
            }
        }

        return $method;
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        $json = self::json();
        return $_POST[$key] ?? $_GET[$key] ?? $json[$key] ?? $default;
    }

    public static function all(): array
    {
        return array_merge($_GET, $_POST, self::json());
    }

    public static function json(): array
    {
        if (self::$jsonPayload !== null) {
            return self::$jsonPayload;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
        if (!str_contains($contentType, 'application/json')) {
            self::$jsonPayload = [];
            return self::$jsonPayload;
        }

        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw ?: '[]', true);
        self::$jsonPayload = is_array($decoded) ? $decoded : [];
        return self::$jsonPayload;
    }

    public static function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public static function isApi(): bool
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = rtrim((string) config('app.base_url'), '/');
        if ($base && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base)) ?: '/';
        }

        return str_starts_with($uri, '/api/');
    }

    public static function verifyCsrf(): void
    {
        if (self::method() === 'GET' || self::isApi()) {
            return;
        }

        $token = $_POST['_token'] ?? '';
        if (!$token || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
            http_response_code(419);
            View::render('errors/419', [], 'layouts/minimal');
            exit;
        }
    }
}
