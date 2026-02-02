<?php
/**
 * Render a public fixture row.
 *
 * Variables expected:
 * @var array $fixture The fixture data.
 * @var bool $showResult Whether to show the result (score) or just time/vs. Default true.
 * @var bool $showDate Whether to show the date above the fixture. Default false.
 * @var bool $showCompetition Whether to show competition name below. Default true.
 */

// Defaults
$showResult = $showResult ?? true;
$showDate = $showDate ?? false;
$showCompetition = $showCompetition ?? true;

$homeTeam = $fixture['homeTeam'] ?? null;
$awayTeam = $fixture['awayTeam'] ?? null;
$result = $fixture['result'] ?? null;

$dateDisplay = '-';
if (!empty($fixture['date'])) {
    $dateObj = new DateTime($fixture['date']);
    $dateDisplay = $dateObj->format('D j M');
}

$time = $fixture['time'] ?? '15:00';

$homeColour = htmlspecialchars($homeTeam['colour'] ?? '#333333');
$awayColour = htmlspecialchars($awayTeam['colour'] ?? '#333333');
$homeName = htmlspecialchars($homeTeam['name'] ?? 'TBD');
$awayName = htmlspecialchars($awayTeam['name'] ?? 'TBD');
$homeId = htmlspecialchars($homeTeam['id'] ?? '');
$awayId = htmlspecialchars($awayTeam['id'] ?? '');

$competition = htmlspecialchars($fixture['competitionName'] ?? '');
if (!empty($fixture['roundName'])) {
    $competition .= ' - ' . htmlspecialchars($fixture['roundName']);
}

$scoreHtml = '';
// Check if result exists and we should show it
if ($showResult && $result !== null) {
    // Basic score (90 mins)
    $homeScore = (int) ($result['homeScore'] ?? 0);
    $awayScore = (int) ($result['awayScore'] ?? 0);

    // Build the details string for AET and Penalties
    $details = [];

    // Check for extra time
    if (!empty($result['extraTime'])) {
        $etHome = $result['homeScoreET'] ?? $homeScore;
        $etAway = $result['awayScoreET'] ?? $awayScore;
        $details[] = "{$etHome} - {$etAway} AET";
    }

    // Check for penalties
    if (!empty($result['penalties'])) {
        $pHome = $result['homePens'] ?? 0;
        $pAway = $result['awayPens'] ?? 0;
        $details[] = "{$pHome} - {$pAway} pens";
    }

    $detailsHtml = '';
    if (!empty($details)) {
        $detailsStr = '(' . implode(', ', $details) . ')';
        $detailsHtml = '<div class="text-[10px] text-text-muted font-normal mt-0.5 whitespace-nowrap">' . $detailsStr . '</div>';
    }

    $scoreHtml = '<div class="flex flex-col items-center justify-center">';
    $scoreHtml .= '<div class="font-bold text-xl text-primary bg-surface-hover px-3 py-1 rounded-sm leading-none">' . $homeScore . ' - ' . $awayScore . '</div>';
    $scoreHtml .= $detailsHtml;
    $scoreHtml .= '</div>';
} else {
    // If no result or hidden, show time or 'vs'
    $scoreHtml = '<div class="text-base text-text-muted bg-transparent font-medium">' . htmlspecialchars($time) . '</div>';
}

$homeSlug = htmlspecialchars($homeTeam['slug'] ?? $homeId);
$awaySlug = htmlspecialchars($awayTeam['slug'] ?? $awayId);

$homeLink = $homeId ? "<a href=\"{$basePath}/team/{$homeSlug}\" class=\"hover:text-primary transition-colors\">{$homeName}</a>" : $homeName;
$awayLink = $awayId ? "<a href=\"{$basePath}/team/{$awaySlug}\" class=\"hover:text-primary transition-colors\">{$awayName}</a>" : $awayName;
?>

<li
    class="flex flex-col items-center py-4 border-b border-border last:border-b-0 gap-1 hover:bg-surface-hover/50 transition-colors px-4 -mx-4 rounded-sm">
    <?php if ($showDate): ?>
        <div class="text-xs text-text-muted font-bold uppercase tracking-wider mb-1">
            <?= $dateDisplay ?>
        </div>
    <?php endif; ?>

    <div class="flex items-center justify-center gap-4 md:gap-8 w-full">
        <div class="flex-1 flex items-center justify-end gap-3 font-semibold text-right">
            <?= $homeLink ?>
            <span class="inline-block w-4 h-4 rounded-full bg-current shadow-sm"
                style="color: <?= $homeColour ?>; background-color: <?= $homeColour ?>"></span>
        </div>
        <?= $scoreHtml ?>
        <div class="flex-1 flex items-center justify-start gap-3 font-semibold text-left">
            <span class="inline-block w-4 h-4 rounded-full bg-current shadow-sm"
                style="color: <?= $awayColour ?>; background-color: <?= $awayColour ?>"></span>
            <?= $awayLink ?>
        </div>
    </div>

    <?php if ($showCompetition): ?>
        <div class="text-xs text-text-muted font-medium text-center">
            <?= $competition ?>
        </div>
    <?php endif; ?>
</li>