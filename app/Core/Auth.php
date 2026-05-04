<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    private static bool $resolved = false;
    private static ?array $resolvedUser = null;

    public static function user(): ?array
    {
        if (self::$resolved) {
            return self::$resolvedUser;
        }

        $userModel = new User();

        if (!empty($_SESSION['auth_user_id'])) {
            self::$resolvedUser = $userModel->findActiveById((int) $_SESSION['auth_user_id']);
            self::$resolved = true;
            return self::$resolvedUser;
        }

        $bearerToken = self::bearerToken();
        if ($bearerToken) {
            self::$resolvedUser = $userModel->findByApiToken($bearerToken);
            self::$resolved = true;
            return self::$resolvedUser;
        }

        self::$resolved = true;
        self::$resolvedUser = null;
        return null;
    }

    public static function attempt(string $email, string $password): bool
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !$user['password'] || !password_verify($password, $user['password'])) {
            return false;
        }

        if ($user['deleted_at']) {
            flash('error', 'Your account is deactivated. Use the reactivation form.');
            return false;
        }

        $_SESSION['auth_user_id'] = (int) $user['id'];
        self::$resolved = false;
        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION['auth_user_id']);
        self::$resolved = false;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        return self::check() && self::user()['role'] === 'admin';
    }

    private static function bearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!$header && function_exists('getallheaders')) {
            $headers = getallheaders();
            $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (preg_match('/Bearer\s+(.+)/i', $header, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}
