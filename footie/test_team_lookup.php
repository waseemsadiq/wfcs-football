#!/usr/bin/env php
<?php
// Load environment
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
        $_ENV[trim($key)] = trim($value);
    }
}

require __DIR__ . '/core/Model.php';
require __DIR__ . '/app/Models/Team.php';

$teamModel = new \App\Models\Team();

echo "Testing findWhere for 'al-ain-whites':\n";
$team = $teamModel->findWhere('slug', 'al-ain-whites');

if ($team) {
    echo "✓ Team found!\n";
    echo "  Keys: " . implode(', ', array_keys($team)) . "\n";
    echo "  ID: " . ($team['id'] ?? 'MISSING') . "\n";
    echo "  Name: " . ($team['name'] ?? 'MISSING') . "\n";
} else {
    echo "✗ Team NOT found\n";
}

echo "\nTesting other teams:\n";
$teams = ['athletico-pinks', 'purple-legends', 'triple-7-black'];
foreach ($teams as $slug) {
    $t = $teamModel->findWhere('slug', $slug);
    echo "  $slug: " . ($t ? '✓ Found (ID: ' . $t['id'] . ')' : '✗ Not found') . "\n";
}
