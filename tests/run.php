<?php

require __DIR__ . '/bootstrap.php';

$tests = [
    __DIR__ . '/unit/RateLimiterTest.php',
    __DIR__ . '/unit/ValidatorTest.php',
    __DIR__ . '/unit/RequestTest.php',
    __DIR__ . '/unit/EnvTest.php',
];

$passed = 0;
$failed = 0;

foreach ($tests as $testFile) {
    $name = basename($testFile, '.php');

    try {
        require $testFile;
        echo "[PASS] {$name}\n";
        $passed++;
    } catch (Throwable $exception) {
        echo "[FAIL] {$name}: {$exception->getMessage()}\n";
        $failed++;
    }
}

echo "\nPassed: {$passed}\n";
echo "Failed: {$failed}\n";

exit($failed > 0 ? 1 : 0);
