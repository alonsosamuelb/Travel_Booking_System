<?php

require __DIR__ . '/../bootstrap/app.php';

use App\Core\App;

App::boot();

function test_assert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}
