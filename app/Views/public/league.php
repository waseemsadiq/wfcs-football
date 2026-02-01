<?php
/**
 * Render a fixture row for league page.
 */
?>

<div>
    <div class="mb-4 text-center">
        <h1 class="text-4xl font-extrabold m-0 text-text-main mb-2">
            <?= htmlspecialchars($league['name']) ?>
        </h1>
        <p class="mb-8"><a href="<?= $basePath ?>/"
                class="text-primary hover:text-primary-hover transition-colors font-medium">Back to
                home</a></p>
    </div>

    <!-- Standings -->
    <section class="mb-16">
        <div class="card p-0 overflow-hidden">
            <?php
            $title = 'Standings';
            include __DIR__ . '/../partials/card_header.php';
            ?>
            <div class="p-0">
                <?php
                $context = 'public';
                include __DIR__ . '/../partials/standings_table.php';
                ?>
            </div>
        </div>
    </section>

    <!-- Fixtures -->
    <section class="mb-16">
        <div class="card p-0 overflow-hidden">
            <?php
            $title = 'Fixtures';
            include __DIR__ . '/../partials/card_header.php';
            ?>
            <?php if (empty($fixtures)): ?>
                <?php
                $message = 'No fixtures scheduled';
                include __DIR__ . '/../partials/empty_state.php';
                ?>
            <?php else: ?>
                <ul class="divide-y divide-border">
                    <?php foreach ($fixtures as $fixture): ?>
                        <?php
                        $showDate = true;
                        $showCompetition = false;
                        include __DIR__ . '/../partials/public_fixture.php';
                        ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
</div>