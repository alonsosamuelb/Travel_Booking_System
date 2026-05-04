<?php

use App\Services\RateLimiterService;

$directory = sys_get_temp_dir() . '/travel_booking_rate_tests';
if (is_dir($directory)) {
    foreach (glob($directory . '/*.json') ?: [] as $file) {
        @unlink($file);
    }
}

$limiter = new RateLimiterService($directory);
$key = 'login|127.0.0.1';

test_assert($limiter->tooManyAttempts($key, 2, 60) === false, 'Limiter should allow fresh keys.');
$limiter->hit($key, 60);
$limiter->hit($key, 60);
test_assert($limiter->tooManyAttempts($key, 2, 60) === true, 'Limiter should block after reaching the max attempts.');
$limiter->clear($key);
test_assert($limiter->tooManyAttempts($key, 2, 60) === false, 'Limiter should reset after clear.');
