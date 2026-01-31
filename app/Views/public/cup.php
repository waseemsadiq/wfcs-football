<?php
/**
 * Render a fixture row for cup page.
 */
if (!function_exists('renderCupFixture')) {
    function renderCupFixture(array $fixture): string
    {
        $homeTeam = $fixture['homeTeam'] ?? null;
        $awayTeam = $fixture['awayTeam'] ?? null;
        $result = $fixture['result'] ?? null;

        $date = '';
        if (!empty($fixture['date'])) {
            $dateObj = new DateTime($fixture['date']);
            $date = $dateObj->format('D j M');
        }

        $time = $fixture['time'] ?? '15:00';

        $homeColour = htmlspecialchars($homeTeam['colour'] ?? '#333333');
        $awayColour = htmlspecialchars($awayTeam['colour'] ?? '#333333');
        $homeName = htmlspecialchars($homeTeam['name'] ?? 'TBD');
        $awayName = htmlspecialchars($awayTeam['name'] ?? 'TBD');
        $homeId = htmlspecialchars($homeTeam['id'] ?? '');
        $awayId = htmlspecialchars($awayTeam['id'] ?? '');

        // Check if teams are set
        $hasTeams = $homeTeam && $awayTeam;

        $scoreHtml = '';
        if ($result) {
            $homeScore = (int) ($result['homeScore'] ?? 0);
            $awayScore = (int) ($result['awayScore'] ?? 0);

            // Check for extra time or penalties
            $extra = '';
            if (!empty($result['penalties'])) {
                $extra = ' <span class="text-sm font-normal text-text-muted">(pens)</span>';
            } elseif (!empty($result['extraTime'])) {
                $extra = ' <span class="text-sm font-normal text-text-muted">(aet)</span>';
            }

            $scoreHtml = '<div class="font-bold text-xl text-primary bg-surface-hover px-3 py-1 rounded-sm">' . $homeScore . ' - ' . $awayScore . $extra . '</div>';
        } elseif ($hasTeams) {
            $scoreHtml = '<div class="text-base text-text-muted bg-transparent font-medium">' . htmlspecialchars($time) . '</div>';
        } else {
            $scoreHtml = '<div class="text-base text-text-muted bg-transparent font-medium">vs</div>';
        }

        $homeSlug = htmlspecialchars($homeTeam['slug'] ?? $homeId);
        $awaySlug = htmlspecialchars($awayTeam['slug'] ?? $awayId);

        $homeLink = $homeId ? "<a href=\"/team/{$homeSlug}\" class=\"hover:text-primary transition-colors\">{$homeName}</a>" : $homeName;
        $awayLink = $awayId ? "<a href=\"/team/{$awaySlug}\" class=\"hover:text-primary transition-colors\">{$awayName}</a>" : $awayName;

        $dateDisplay = $date ?: '-';

        return <<<HTML
        <li class="flex flex-col items-center py-4 border-b border-border last:border-b-0 gap-1 hover:bg-surface-hover/50 transition-colors px-4 -mx-4 rounded-sm">
            <div class="text-xs text-text-muted font-bold uppercase tracking-wider mb-1">{$dateDisplay}</div>
            <div class="flex items-center justify-center gap-4 md:gap-8 w-full">
                <div class="flex-1 flex items-center justify-end gap-3 font-semibold text-right">
                    {$homeLink}
                    <span class="inline-block w-4 h-4 rounded bg-current shadow-sm" style="color: {$homeColour}; background-color: {$homeColour}"></span>
                </div>
                {$scoreHtml}
                <div class="flex-1 flex items-center justify-start gap-3 font-semibold text-left">
                    <span class="inline-block w-4 h-4 rounded bg-current shadow-sm" style="color: {$awayColour}; background-color: {$awayColour}"></span>
                    {$awayLink}
                </div>
            </div>
        </li>
HTML;
    }
}
?>

<div class="container">
    <div class="mb-4 text-center">
        <h1
            class="text-4xl font-extrabold m-0 text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400 mb-2">
            <?= htmlspecialchars($cup['name']) ?>
        </h1>
        <p class="mb-8"><a href="<?=$basePath?>/" class="text-primary hover:text-primary-hover transition-colors font-medium">Back to
                home</a></p>
    </div>

    <!-- Cup Rounds -->
    <?php if (empty($rounds)): ?>
        <div class="text-center py-12 text-text-muted">
            <p>No fixtures scheduled</p>
        </div>
    <?php else: ?>
        <div class="space-y-8 max-w-4xl mx-auto">
            <?php foreach ($rounds as $round): ?>
                <div class="bg-surface rounded-md shadow-glow border border-border overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-border bg-surface-hover/50 border-l-4 border-l-primary flex items-center gap-2">
                        <span class="text-lg font-bold text-text-main"><?= htmlspecialchars($round['name']) ?></span>
                    </div>
                    <div class="p-6">
                        <?php if (empty($round['fixtures'])): ?>
                            <div class="text-center py-8 text-text-muted">
                                <p>No fixtures in this round</p>
                            </div>
                        <?php else: ?>
                            <ul class="divide-y divide-border">
                                <?php foreach ($round['fixtures'] as $fixture): ?>
                                    <?= renderCupFixture($fixture) ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>