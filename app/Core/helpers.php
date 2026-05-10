<?php

use App\Core\App;

function config(string $key, mixed $default = null): mixed
{
    return App::config($key, $default);
}

function base_url(string $path = ''): string
{
    $base = rtrim((string) config('app.base_url', ''), '/');
    $path = ltrim($path, '/');

    return $path ? $base . '/' . $path : $base;
}

function asset(string $path): string
{
    return base_url($path);
}

function redirect_to(string $path): never
{
    header('Location: ' . base_url($path));
    exit;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function flash(string $key, mixed $value): void
{
    $_SESSION['_flash'][$key] = $value;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function is_active(string $needle): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return str_contains($uri, $needle) ? 'active' : '';
}

function trip_image_url(?string $path): string
{
    $path = trim((string) $path);
    return $path !== '' ? $path : (string) config('app.default_trip_image');
}

function url_with_query(string $path, array $updates = []): string
{
    $query = array_merge($_GET, $updates);
    $query = array_filter(
        $query,
        static fn (mixed $value): bool => $value !== null && $value !== ''
    );

    $url = base_url($path);
    if ($query === []) {
        return $url;
    }

    return $url . '?' . http_build_query($query);
}
