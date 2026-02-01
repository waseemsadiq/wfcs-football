<div class="container">
    <div class="mb-4 text-center">
        <h1 class="text-4xl font-extrabold m-0 text-text-main mb-2">
            <?= htmlspecialchars($cup['name']) ?>
        </h1>
        <p class="mb-8"><a href="<?= $basePath ?>/"
                class="text-primary hover:text-primary-hover transition-colors font-medium">Back to
                home</a></p>
    </div>

    <!-- Cup Rounds -->
    <?php if (empty($rounds)): ?>
        <?php
        $message = 'No fixtures scheduled';
        include __DIR__ . '/../partials/empty_state.php';
        ?>
    <?php else: ?>
        <div class="space-y-8">
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
                                    <?php include __DIR__ . '/../partials/public_fixture.php'; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>