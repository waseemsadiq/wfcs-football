<div>
    <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold tracking-tight mb-2 text-text-main">
            Football</h1>
        <?php if (isset($seasonName) && $seasonName): ?>
            <p class="text-text-muted text-lg font-medium"><?= htmlspecialchars($seasonName) ?> Season</p>
        <?php endif; ?>
    </div>

    <!-- Recent Results -->
    <section class="mb-16">
        <div class="card p-0 overflow-hidden">
            <?php
            $title = 'Recent Results';
            include __DIR__ . '/../partials/card_header.php';
            ?>
            <?php if (empty($recentResults)): ?>
                <div class="text-center py-12 text-text-muted">
                    <p>No recent results</p>
                </div>
            <?php else: ?>
                <?php
                $groupedResults = [];
                foreach ($recentResults as $fixture) {
                    $date = $fixture['date'] ?? 'TBD';
                    $groupedResults[$date][] = $fixture;
                }
                ?>
                <div class="flex flex-col">
                    <?php foreach ($groupedResults as $date => $fixtures): ?>
                        <div class="bg-surface border-l-4 border-l-primary border-b border-border py-2 text-center">
                            <span class="text-xs font-bold text-text-muted uppercase tracking-wider">
                                <?= $date !== 'TBD' ? date('D j M', strtotime($date)) : 'TBD' ?>
                            </span>
                        </div>
                        <ul class="divide-y divide-border border-b border-border last:border-0 px-6">
                            <?php foreach ($fixtures as $fixture): ?>
                                <?php include __DIR__ . '/../partials/public_fixture.php'; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Upcoming Fixtures -->
    <section class="mb-16">
        <div class="card p-0 overflow-hidden">
            <?php
            $title = 'Upcoming Fixtures';
            include __DIR__ . '/../partials/card_header.php';
            ?>
            <?php if (empty($upcomingFixtures)): ?>
                <div class="text-center py-12 text-text-muted">
                    <p>No upcoming fixtures</p>
                </div>
            <?php else: ?>
                <?php
                $groupedUpcoming = [];
                foreach ($upcomingFixtures as $fixture) {
                    $date = $fixture['date'] ?? 'TBD';
                    $groupedUpcoming[$date][] = $fixture;
                }
                ?>
                <div class="flex flex-col">
                    <?php foreach ($groupedUpcoming as $date => $fixtures): ?>
                        <div class="bg-surface border-l-4 border-l-primary border-b border-border py-2 text-center">
                            <span class="text-xs font-bold text-text-muted uppercase tracking-wider">
                                <?= $date !== 'TBD' ? date('D j M', strtotime($date)) : 'TBD' ?>
                            </span>
                        </div>
                        <ul class="divide-y divide-border border-b border-border last:border-0 px-6">
                            <?php foreach ($fixtures as $fixture): ?>
                                <?php
                                $showResult = false;
                                include __DIR__ . '/../partials/public_fixture.php';
                                ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
        <!-- Leagues -->
        <div class="card p-0 overflow-hidden">
            <div class="p-6 border-b border-border bg-surface/50">
                <h2 class="text-xl font-bold m-0">Leagues</h2>
            </div>
            <div class="p-6">
                <?php if (empty($leagues)): ?>
                    <div class="text-center py-8 text-text-muted">
                        <p>No active leagues</p>
                    </div>
                <?php else: ?>
                    <ul class="space-y-2">
                        <?php foreach ($leagues as $league): ?>
                            <li>
                                <a href="<?= $basePath ?>/league/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
                                    class="flex items-center gap-3 p-3 rounded-sm hover:bg-surface-hover transition-colors group">
                                    <svg class="w-5 h-5 text-primary group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 3h12c1.1 0 2 .9 2 2v2c0 1.1-.9 2-2 2h-.5c0 3.32-2.28 6.1-5.34 6.84l-.66.66V19h2c1.1 0 2 .9 2 2v1H8v-1c0-1.1.9-2 2-2h2v-2.5l-.66-.66C8.28 15.1 6 12.32 6 9h-.5c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2zm2 6h8c0 2.21-1.79 4-4 4s-4-1.79-4-4z"/>
                                    </svg>
                                    <span
                                        class="font-semibold text-text-main group-hover:text-primary transition-colors"><?= htmlspecialchars($league['name']) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cups -->
        <div class="card p-0 overflow-hidden">
            <div class="p-6 border-b border-border bg-surface/50">
                <h2 class="text-xl font-bold m-0">Cups</h2>
            </div>
            <div class="p-6">
                <?php if (empty($cups)): ?>
                    <div class="text-center py-8 text-text-muted">
                        <p>No active cups</p>
                    </div>
                <?php else: ?>
                    <ul class="space-y-2">
                        <?php foreach ($cups as $cup): ?>
                            <li>
                                <a href="<?= $basePath ?>/cup/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>"
                                    class="flex items-center gap-3 p-3 rounded-sm hover:bg-surface-hover transition-colors group">
                                    <span class="text-xl group-hover:scale-110 transition-transform">🏅</span>
                                    <span
                                        class="font-semibold text-text-main group-hover:text-primary transition-colors"><?= htmlspecialchars($cup['name']) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>