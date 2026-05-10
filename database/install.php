<?php

require __DIR__ . '/../bootstrap/app.php';

use App\Core\App;
use App\Core\Database;

App::boot();

$schemaPath = __DIR__ . '/schema.sql';
if (!is_file($schemaPath)) {
    exit("Schema file not found.\n");
}

$sql = (string) file_get_contents($schemaPath);
if (trim($sql) === '') {
    exit("Schema file is empty.\n");
}

$config = App::config('database');

try {
    try {
        $db = Database::connection();
    } catch (\Throwable) {
        $server = Database::makeConnection($config, false);
        $databaseName = str_replace('`', '``', $config['database']);
        $server->exec("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET {$config['charset']} COLLATE {$config['charset']}_unicode_ci");
        Database::reset();
        $db = Database::connection();
    }

    $db->exec($sql);

    echo "Schema imported successfully.\n";
} catch (\Throwable $exception) {
    exit("Install failed: {$exception->getMessage()}\nIf you are using shared hosting, create the database first from the hosting panel and run this script again.\n");
}
