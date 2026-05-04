<?php

require __DIR__ . '/../bootstrap/app.php';

use App\Core\App;
use App\Services\MigrationService;

App::boot();

$service = new MigrationService();
$executed = $service->migrate();

echo "Migrations completed.\n";
if ($executed) {
    foreach ($executed as $migration) {
        echo "Applied: {$migration}\n";
    }
} else {
    echo "No pending migrations.\n";
}
