<div>
    <div class="mb-12 text-center">
        <div class="flex items-center justify-center gap-4 mb-2">
            <div class="w-8 h-8 rounded-full shadow-sm ring-1 ring-border"
                style="background-color: <?= htmlspecialchars($team['colour'] ?? '#333333') ?>">
            </div>
            <h1 class="text-4xl font-extrabold m-0 text-text-main">
                <?= htmlspecialchars($team['name']) ?>
            </h1>
        </div>



        <?php if (!empty($competitions)): ?>
            <div class="flex flex-wrap gap-2 justify-center mb-4">
                <?php foreach ($competitions as $comp): ?>
                    <a href="<?= $basePath . htmlspecialchars($comp['url']) ?>"
                        class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-primary/10 text-primary hover:bg-primary/20 transition-colors border border-primary/20 uppercase tracking-wide gap-1">
                        <?= htmlspecialchars($comp['name']) ?>
                        <?php if (!empty($comp['detail'])): ?>
                            <span class="text-text-muted font-medium">(<?= htmlspecialchars($comp['detail']) ?>)</span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php
    // Split fixtures into recent matches (with results) and upcoming (without results)
    $recentMatches = array_filter($fixtures, fn($f) => isset($f['result']) && $f['result'] !== null);
    $upcomingFixtures = array_filter($fixtures, fn($f) => !isset($f['result']) || $f['result'] === null);

    // Sort recent matches: most recent first (descending)
    usort($recentMatches, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));

    // Sort upcoming fixtures: soonest first (ascending)
    usort($upcomingFixtures, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));
    ?>

    <!-- Upcoming Fixtures -->
    <section class="mb-8">
        <div class="card p-0 overflow-hidden">
            <?php
            $title = 'Upcoming Fixtures';
            include __DIR__ . '/../partials/card_header.php';
            ?>
            <?php if (empty($upcomingFixtures)): ?>
                <div class="text-center py-8 text-text-muted text-sm">
                    <p>No upcoming fixtures</p>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-border">
                    <?php foreach ($upcomingFixtures as $fixture): ?>
                        <?php
                        $showDate = true;
                        include __DIR__ . '/../partials/public_fixture.php';
                        ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>

    <!-- Recent Matches -->
    <section class="mb-16">
        <div class="card p-0 overflow-hidden">
            <?php
            $title = 'Recent Matches';
            include __DIR__ . '/../partials/card_header.php';
            ?>
            <?php if (empty($recentMatches)): ?>
                <div class="text-center py-8 text-text-muted text-sm">
                    <p>No recent matches</p>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-border">
                    <?php foreach ($recentMatches as $fixture): ?>
                        <?php
                        $showDate = true;
                        include __DIR__ . '/../partials/public_fixture.php';
                        ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
</div>