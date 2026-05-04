<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\View;

class RoleMiddleware
{
    public function handle(string $role): void
    {
        $user = Auth::user();

        if (!$user || $user['role'] !== $role) {
            if (Request::isApi()) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Forbidden']);
                exit;
            }

            http_response_code(403);
            View::render('errors/403', [], 'layouts/minimal');
            exit;
        }
    }
}
