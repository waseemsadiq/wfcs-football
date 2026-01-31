<?php
require_once __DIR__ . '/index.php'; // Bootstrap

use App\Models\League;
use App\Controllers\DashboardController;

// Mock session
$_SESSION['user_id'] = '123';

$controller = new DashboardController();
$leagueModel = new League();

// Find "Oldies"
$leagues = $leagueModel->all();
$oldies = null;
foreach ($leagues as $l) {
    if ($l['name'] === 'Oldies') {
        $oldies = $l;
        break;
    }
}

if (!$oldies) {
    echo "Oldies league not found\n";
    exit;
}

echo "Found Oldies: " . $oldies['id'] . "\n";
echo "Fixtures count: " . count($oldies['fixtures']) . "\n";

// Helper to access private method if needed, or just copy logic here to test.
// Since filterUpcoming is private, let's reflect it or just copy logic.

$fixtures = $oldies['fixtures'];

// 1. Filter
$unplayed = array_filter($fixtures, function ($f) {
    return empty($f['result']);
});
echo "Unplayed count: " . count($unplayed) . "\n";

if (empty($unplayed)) {
    echo "No unplayed fixtures\n";
    exit;
}

// 2. Sort
usort($unplayed, function ($a, $b) {
    $dateA = $a['date'] . ' ' . ($a['time'] ?? '00:00');
    $dateB = $b['date'] . ' ' . ($b['time'] ?? '00:00');
    return strcmp($dateA, $dateB);
});

// 3. Group
$firstFixture = reset($unplayed);
$nextDate = $firstFixture['date'];
echo "Next date: " . $nextDate . "\n";

$nextRound = array_filter($unplayed, function ($f) use ($nextDate) {
    return $f['date'] === $nextDate;
});
echo "Next round count: " . count($nextRound) . "\n";

print_r($nextRound);
