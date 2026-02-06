#!/usr/bin/env php
<?php
/**
 * Migration 003: Add referee role to team_staff
 */

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

require __DIR__ . '/core/Database.php';

try {
    $db = Core\Database::getInstance();

    echo "=== Migration 003: Add referee role ===\n\n";

    echo "Updating team_staff role enum to include 'referee'...\n";
    $sql = "ALTER TABLE team_staff MODIFY COLUMN role ENUM('coach', 'assistant_coach', 'manager', 'referee', 'contact', 'other') NOT NULL";
    $db->query($sql);
    echo "✓ Role enum updated\n\n";

    echo "=== Migration Complete ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
