#!/usr/bin/env php
<?php
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

$db = Core\Database::getInstance();

echo "Checking team_staff role column:\n";
$stmt = $db->query('SHOW COLUMNS FROM team_staff WHERE Field = "role"');
$result = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($result);

echo "\n\nChecking players table for duplicate slugs:\n";
$stmt = $db->query('SELECT slug, COUNT(*) as count FROM players GROUP BY slug HAVING count > 1');
$duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($duplicates)) {
    echo "No duplicate slugs found.\n";
} else {
    print_r($duplicates);
}
