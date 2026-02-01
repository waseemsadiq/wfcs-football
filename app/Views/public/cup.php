<div>
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
        <div class="space-y-16">
            <?php foreach ($rounds as $round): ?>
                <section>
                    <div class="card p-0 overflow-hidden">
                        <?php
                        $title = $round['name'];
                        include __DIR__ . '/../partials/card_header.php';
                        ?>
                        <?php if (empty($round['fixtures'])): ?>
                            <div class="text-center py-8 text-text-muted p-6">
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
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>