<?php
/**
 * Render a fixture row for league page.
 */
if (!function_exists('renderLeagueFixture')) {
    function renderLeagueFixture(array $fixture): string
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
        </li>
HTML;
    }
}
?>

<div class="container">
    <div class="mb-4 text-center">
        <h1
            class="text-4xl font-extrabold m-0 text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400 mb-2">
            <?= htmlspecialchars($league['name']) ?>
        </h1>
        <p class="mb-8"><a href="<?= $basePath ?>/"
                class="text-primary hover:text-primary-hover transition-colors font-medium">Back to
                home</a></p>
    </div>

    <!-- Standings -->
    <section class="mb-16">
        <div class="flex items-center gap-4 mb-6">
            <h2 class="text-2xl font-bold">Standings</h2>
            <div class="h-px bg-border flex-1"></div>
        </div>
        <div class="card p-0 overflow-hidden">
            <div class="p-0">
                <?php if (empty($standings)): ?>
                    <div class="text-center py-12 text-text-muted">
                        <p>No standings available</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th
                                        class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12">
                                        Pos</th>
                                    <th
                                        class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-left">
                                        Team</th>
                                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12"
                                        title="Played">P</th>
                                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden sm:table-cell"
                                        title="Won">W</th>
                                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden sm:table-cell"
                                        title="Drawn">D</th>
                                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden sm:table-cell"
                                        title="Lost">L</th>
                                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden md:table-cell"
                                        title="Goals For">GF</th>
                                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden md:table-cell"
                                        title="Goals Against">GA</th>
                                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12"
                                        title="Goal Difference">GD</th>
                                    <th
                                        class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-16">
                                        Pts</th>
                                    <th
                                        class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-left w-32 hidden md:table-cell">
                                        Form</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $pos = 1;
                                foreach ($standings as $row): ?>
                                    <tr
                                        class="border-b border-border hover:bg-surface-hover/50 transition-colors last:border-0">
                                        <td class="p-4 text-center font-medium text-text-muted"><?= $pos++ ?></td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-block w-3 h-3 rounded-sm shadow-sm flex-shrink-0"
                                                    style="background-color: <?= htmlspecialchars($row['teamColour']) ?>"></span>
                                                <span class="font-semibold text-text-main">
                                                    <a href="<?= $basePath ?>/team/<?= htmlspecialchars($row['teamSlug'] ?? $row['teamId']) ?>"
                                                        class="hover:text-primary transition-colors">
                                                        <?= htmlspecialchars($row['teamName']) ?>
                                                    </a>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-center text-text-muted"><?= $row['played'] ?></td>
                                        <td class="p-4 text-center text-text-muted hidden sm:table-cell"><?= $row['won'] ?></td>
                                        <td class="p-4 text-center text-text-muted hidden sm:table-cell"><?= $row['drawn'] ?>
                                        </td>
                                        <td class="p-4 text-center text-text-muted hidden sm:table-cell"><?= $row['lost'] ?>
                                        </td>
                                        <td class="p-4 text-center text-text-muted hidden md:table-cell"><?= $row['goalsFor'] ?>
                                        </td>
                                        <td class="p-4 text-center text-text-muted hidden md:table-cell">
                                            <?= $row['goalsAgainst'] ?>
                                        </td>
                                        <td
                                            class="p-4 text-center font-medium <?= $row['goalDifference'] > 0 ? 'text-primary' : ($row['goalDifference'] < 0 ? 'text-danger' : 'text-text-muted') ?>">
                                            <?= $row['goalDifference'] > 0 ? '+' . $row['goalDifference'] : $row['goalDifference'] ?>
                                        </td>
                                        <td class="p-4 text-center font-bold text-lg text-white"><?= $row['points'] ?></td>
                                        <td class="p-4 hidden md:table-cell">
                                            <div class="flex items-center justify-start gap-1">
                                                <?php if (!empty($row['form'])): ?>
                                                    <?php foreach ($row['form'] as $result): ?>
                                                        <?php
                                                        $colorClass = match ($result) {
                                                            'W' => 'bg-green-500 text-white',
                                                            'D' => 'bg-gray-500 text-white',
                                                            'L' => 'bg-danger text-white',
                                                            default => 'bg-gray-700 text-gray-300'
                                                        };
                                                        ?>
                                                        <span
                                                            class="w-5 h-5 flex items-center justify-center rounded text-[10px] font-bold <?= $colorClass ?>"
                                                            title="<?= $result ?>">
                                                            <?= $result ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-text-muted text-xs">-</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

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
                        <?= renderLeagueFixture($fixture) ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
</div>