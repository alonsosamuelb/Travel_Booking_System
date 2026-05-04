<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            if (Request::isApi()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['message' => 'Unauthenticated']);
                exit;
            }

            flash('error', 'Please sign in first.');
            redirect_to('login');
        }
    }
}
