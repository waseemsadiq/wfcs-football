# Footie Admin Model Reference

Use these patterns when writing admin scripts.

## Core Models

### Team

Properties: `id`, `name`, `slug`, `contact`, `phone`, `email`, `colour`, `players` (array)

```php
$teamModel = new \App\Models\Team();

// Create
$newTeam = $teamModel->create([
    'name' => 'Reform FC',
    'contact' => 'John Smith',
    'email' => 'john@example.com',
    'colour' => '#1a5f2a',
    'players' => ['Player One', 'Player Two']
]);
echo "Created team: " . $newTeam['name'] . " (ID: " . $newTeam['id'] . ")\n";

// Find
$team = $teamModel->findWhere('slug', 'reform-fc');

// Update
$teamModel->update($team['id'], [
    'name' => 'Reform Athletic',
    'colour' => '#ff0000'
]);

// Delete
$teamModel->delete($team['id']);
```

### Season

Properties: `id`, `name`, `start_date`, `end_date`, `is_active`

```php
$seasonModel = new \App\Models\Season();

// Create Active Season (Handles deactivating others automatically if logic exists, check model)
$seasonModel->create([
    'name' => '2024/2025',
    'start_date' => '2024-09-01',
    'end_date' => '2025-05-31',
    'is_active' => 1
]);

// Get Active
$active = $seasonModel->getActive();
```

### League

Properties: `id`, `season_id`, `name`, `type` (5-a-side, 7-a-side), `win_points`, `draw_points`, `loss_points`

```php
$leagueModel = new \App\Models\League();

$leagueModel->create([
    'season_id' => $activeSeasonId,
    'name' => 'Monday Night League',
    'type' => '5-a-side', // verify valid types in code
    'win_points' => 3,
    'draw_points' => 1,
    'loss_points' => 0
]);
```

### Cup

Properties: `id`, `season_id`, `name`, `type` (knockout, group+knockout)

```php
$cupModel = new \App\Models\Cup();
$cupModel->create([
    'season_id' => $activeSeasonId,
    'name' => 'Winter Cup',
    'type' => 'knockout'
]);
```

## Useful Snippets

### Generating a Safe Slug (If not using Model create)

```php
function slugify($text) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
}
```

### Iterate All Teams

```php
$teams = $teamModel->all();
foreach ($teams as $t) {
    echo "Processing " . $t['name'] . "\n";
}
```
