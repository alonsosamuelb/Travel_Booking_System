<?php

require __DIR__ . '/../bootstrap/app.php';

use App\Core\App;
use App\Services\MigrationService;

App::boot();

$service = new MigrationService();
$service->seed();

echo "Seed completed.\n";
