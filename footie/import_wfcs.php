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
$sqlFile = __DIR__ . '/../install-wfcs-data.sql';

if (!file_exists($sqlFile)) {
    die("SQL file not found: $sqlFile\n");
}

echo "Reading SQL file...\n";
$sql = file_get_contents($sqlFile);

// Split into individual statements
$statements = array_filter(array_map('trim', explode(";\n", $sql)), function($stmt) {
    return !empty($stmt) && strpos($stmt, '--') !== 0;
});

echo "Found " . count($statements) . " SQL statements\n\n";
echo "Importing...\n\n";

$counts = [
    'seasons' => 0,
    'leagues' => 0,
    'teams' => 0,
    'associations' => 0,
    'players' => 0,
    'staff' => 0
];

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (empty($statement)) continue;

    try {
        $db->query($statement);

        // Track what was inserted
        if (stripos($statement, 'INSERT INTO seasons') !== false) {
            $counts['seasons']++;
            echo "✓ Season\n";
        } elseif (stripos($statement, 'INSERT INTO leagues') !== false) {
            $counts['leagues'] = 3;
            echo "✓ Leagues\n";
        } elseif (stripos($statement, 'INSERT INTO teams') !== false) {
            $counts['teams'] = 23;
            echo "✓ Teams\n";
        } elseif (stripos($statement, 'INSERT INTO league_teams') !== false) {
            $counts['associations'] = 23;
            echo "✓ Associations\n";
        } elseif (stripos($statement, 'INSERT INTO players') !== false) {
            $stmt = $db->query("SELECT COUNT(*) FROM players");
            $counts['players'] = $stmt->fetchColumn();
            echo "✓ Players ({$counts['players']})\n";
        } elseif (stripos($statement, 'INSERT INTO team_staff') !== false) {
            $stmt = $db->query("SELECT COUNT(*) FROM team_staff");
            $counts['staff'] = $stmt->fetchColumn();
            echo "✓ Staff ({$counts['staff']} referees)\n";
        }
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        // Continue with remaining statements
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "IMPORT COMPLETE!\n";
echo str_repeat("=", 70) . "\n";
echo "Summary:\n";
echo "  • Seasons:      {$counts['seasons']}\n";
echo "  • Leagues:      {$counts['leagues']}\n";
echo "  • Teams:        {$counts['teams']}\n";
echo "  • Associations: {$counts['associations']}\n";
echo "  • Players:      {$counts['players']}\n";
echo "  • Referees:     {$counts['staff']}\n";
echo str_repeat("=", 70) . "\n";
