<?php

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require __DIR__ . '/../app/Core/Env.php';

\App\Core\Env::load(__DIR__ . '/../.env');

$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone']);

if (session_status() === PHP_SESSION_NONE) {
    if (PHP_SAPI === 'cli') {
        $sessionPath = sys_get_temp_dir() . '/travel_booking_sessions';
        if (!is_dir($sessionPath)) {
            mkdir($sessionPath, 0775, true);
        }
        session_save_path($sessionPath);
    }

    session_start();
}

require __DIR__ . '/../app/Core/helpers.php';
