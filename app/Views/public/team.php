<div class="container">
    <div class="mb-8">
        <a href="<?= $basePath ?>/"
            class="text-primary hover:text-primary-hover transition-colors font-medium flex items-center gap-2">
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
        <?php
        $title = 'Fixtures';
        include __DIR__ . '/../partials/section_header.php';
        ?>
        <div class="card">
            <?php if (empty($fixtures)): ?>
                <div class="text-center py-12 text-text-muted">
                    <p>No fixtures scheduled</p>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-border">
                    <?php foreach ($fixtures as $fixture): ?>
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