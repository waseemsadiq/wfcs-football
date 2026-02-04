<?php
/**
 * Render upcoming fixtures grouped by date.
 *
 * Variables expected:
 * @var array $fixtures The array of fixture data to display.
 * @var bool $showResult Whether to show results. Default false for upcoming fixtures.
 * @var bool $showCompetition Whether to show competition name. Default true.
 */

// Defaults
$showResult = $showResult ?? false;
$showCompetition = $showCompetition ?? true;

if (empty($fixtures)): ?>
    <div class="text-center py-12 text-text-muted">
        <p>No upcoming fixtures</p>
    </div>
<?php else: ?>
    <?php
    // Group fixtures by date
    $groupedFixtures = [];
    $dates = [];

    foreach ($fixtures as $fixture) {
        $date = $fixture['date'] ?? 'TBD';
        if (!isset($groupedFixtures[$date])) {
            $groupedFixtures[$date] = [];
            $dates[] = $date;
        }
        $groupedFixtures[$date][] = $fixture;
    }

    // Sort dates (TBD goes to end)
    usort($dates, function($a, $b) {
        if ($a === 'TBD') return 1;
        if ($b === 'TBD') return -1;
        return strtotime($a) - strtotime($b);
    });
    ?>

    <div class="flex flex-col">
        <?php foreach ($dates as $date): ?>
            <div class="bg-surface border-l-4 border-l-primary border-b border-border py-2 text-center -mx-6 px-6">
                <span class="text-xs font-bold text-text-muted uppercase tracking-wider">
                    <?= $date !== 'TBD' ? date('D j M', strtotime($date)) : 'TBD' ?>
                </span>
            </div>
            <ul class="divide-y divide-border border-b border-border last:border-0">
                <?php foreach ($groupedFixtures[$date] as $fixture): ?>
                    <?php include __DIR__ . '/public_fixture.php'; ?>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
