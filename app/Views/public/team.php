<?php
/**
 * Render a fixture row for team page.
 */
if (!function_exists('renderTeamFixture')) {
    function renderTeamFixture(array $fixture): string
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

        $competition = htmlspecialchars($fixture['competitionName'] ?? '');
        if (!empty($fixture['roundName'])) {
            $competition .= ' - ' . htmlspecialchars($fixture['roundName']);
        }

        $scoreHtml = '';
        if ($result) {
            $homeScore = (int) ($result['homeScore'] ?? 0);
            $awayScore = (int) ($result['awayScore'] ?? 0);
            $scoreHtml = '<div class="font-bold text-xl text-primary bg-surface-hover px-3 py-1 rounded-sm">' . $homeScore . ' - ' . $awayScore . '</div>';
        } else {
            $scoreHtml = '<div class="text-base text-text-muted bg-transparent font-medium">' . htmlspecialchars($time) . '</div>';
        }

        $homeSlug = htmlspecialchars($homeTeam['slug'] ?? $homeId);
        $awaySlug = htmlspecialchars($awayTeam['slug'] ?? $awayId);

        $homeLink = $homeId ? "<a href=\"/team/{$homeSlug}\" class=\"hover:text-primary transition-colors\">{$homeName}</a>" : $homeName;
        $awayLink = $awayId ? "<a href=\"/team/{$awaySlug}\" class=\"hover:text-primary transition-colors\">{$awayName}</a>" : $awayName;

        return <<<HTML
        <li class="flex flex-col items-center py-4 border-b border-border last:border-b-0 gap-1 hover:bg-surface-hover/50 transition-colors px-4 -mx-4 rounded-sm">
            <div class="text-xs text-text-muted font-bold uppercase tracking-wider mb-1">{$date}</div>
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
            <div class="text-xs text-text-muted font-medium text-center">{$competition}</div>
        </li>
HTML;
    }
}
?>

<div class="container">
    <div class="mb-8">
        <a href="<?=$basePath?>/" class="text-primary hover:text-primary-hover transition-colors font-medium flex items-center gap-2">
            <span aria-hidden="true">&larr;</span> Back to home
        </a>
    </div>

    <div class="flex items-center gap-6 mb-12 bg-surface p-8 rounded-md shadow-glow border border-border">
        <div class="w-16 h-16 rounded-md shadow-lg ring-2 ring-white/10"
            style="background-color: <?= htmlspecialchars($team['colour'] ?? '#333333') ?>">
        </div>
        <div>
            <h1
                class="text-4xl font-extrabold m-0 text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">
                <?= htmlspecialchars($team['name']) ?>
            </h1>

            <?php if (!empty($competitions)): ?>
                <div class="flex flex-wrap gap-2 mt-3">
                    <?php foreach ($competitions as $comp): ?>
                        <a href="<?= $basePath . htmlspecialchars($comp['url']) ?>"
                            class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-white/10 text-white hover:bg-white/20 transition-colors border border-white/10 uppercase tracking-wide gap-1">
                            <?= htmlspecialchars($comp['name']) ?>
                            <?php if (!empty($comp['detail'])): ?>
                                <span class="text-white/60 font-medium">(<?= htmlspecialchars($comp['detail']) ?>)</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Fixtures -->
    <section class="mb-16">
        <div class="flex items-center gap-4 mb-6">
            <h2 class="text-2xl font-bold">Fixtures</h2>
            <div class="h-px bg-border flex-1"></div>
        </div>
        <div class="card">
            <?php if (empty($fixtures)): ?>
                <div class="text-center py-12 text-text-muted">
                    <p>No fixtures scheduled</p>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-border">
                    <?php foreach ($fixtures as $fixture): ?>
                        <?= renderTeamFixture($fixture) ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
</div>