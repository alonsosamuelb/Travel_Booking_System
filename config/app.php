<?php

use App\Core\Env;

return [
    'name' => Env::get('APP_NAME', 'Travel Booking System'),
    'base_url' => Env::get('APP_BASE_URL', '/Travel_Booking_System/public'),
    'timezone' => Env::get('APP_TIMEZONE', 'Europe/Madrid'),
    'environment' => Env::get('APP_ENV', 'local'),
    'debug' => Env::get('APP_DEBUG', true),
    'reservation_limit_per_user' => (int) Env::get('RESERVATION_LIMIT_PER_USER', 3),
    'support_email' => Env::get('SUPPORT_EMAIL', 'support@travelbooking.local'),
];
