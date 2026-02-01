<?php
/**
 * Render a page header with title and optional subtitle.
 *
 * Variables expected:
 * @var string $title - Page title
 * @var string|null $subtitle - Optional subtitle (e.g., season name)
 */
?>

<div class="mb-8 text-center">
    <h1 class="text-4xl font-extrabold m-0 text-text-main mb-2">
        <?= htmlspecialchars($title) ?>
    </h1>
    <?php if (isset($subtitle) && $subtitle): ?>
        <p class="text-text-muted text-lg font-medium mb-4">
            <?= htmlspecialchars($subtitle) ?>
        </p>
    <?php endif; ?>
</div>