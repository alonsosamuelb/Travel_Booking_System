<?php

namespace App\Middleware;

use App\Core\Request;
use App\Services\RateLimiterService;

class ThrottleMiddleware
{
    public function handle(string $scope, string $maxAttempts = '5', string $windowSeconds = '300'): void
    {
        $limiter = new RateLimiterService();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = $scope . '|' . $ip;
        $max = max(1, (int) $maxAttempts);
        $window = max(60, (int) $windowSeconds);

        if ($limiter->tooManyAttempts($key, $max, $window)) {
            $retryAfter = $limiter->retryAfter($key, $window);

            if (Request::isApi()) {
                http_response_code(429);
                header('Content-Type: application/json');
                echo json_encode([
                    'message' => 'Too many attempts. Please try again later.',
                    'retry_after_seconds' => $retryAfter,
                ]);
                exit;
            }

            flash('error', 'Too many attempts. Please try again in about ' . max(1, (int) ceil($retryAfter / 60)) . ' minute(s).');
            redirect_to('login');
        }

        $limiter->hit($key, $window);
    }
}
