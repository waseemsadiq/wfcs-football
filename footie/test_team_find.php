<?php
// Bootstrap
require __DIR__ . '/core/Model.php';
require __DIR__ . '/app/Models/Team.php';

$teamModel = new \App\Models\Team();
$team = $teamModel->findWhere('slug', 'al-ain-whites');

echo "Team found: " . ($team ? 'YES' : 'NO') . "\n";

if ($team) {
    echo "\nTeam array keys: " . implode(', ', array_keys($team)) . "\n";
    echo "\nTeam data:\n";
    foreach ($team as $key => $value) {
        if (is_array($value)) {
            echo "  $key: [" . count($value) . " items]\n";
        } else {
            echo "  $key: $value\n";
        }
    }

    echo "\nHas 'id' key? " . (isset($team['id']) ? 'YES - Value: ' . $team['id'] : 'NO') . "\n";
}
