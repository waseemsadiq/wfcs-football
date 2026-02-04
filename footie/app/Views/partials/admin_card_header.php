<?php
/**
 * Render a standard Admin Card Header (H2 + Create Button).
 *
 * Variables expected:
 * @var string $title - Card heading text (e.g. "All Leagues")
 * @var string|null $createUrl - Optional URL for the create button
 * @var string|null $createText - Optional text for the create button
 */
?>
<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-bold m-0">
        <?= htmlspecialchars($title) ?>
    </h2>
    <?php if (!empty($createUrl) && !empty($createText)): ?>
        <a href="<?= $createUrl ?>" class="btn btn-primary">
            <?= htmlspecialchars($createText) ?>
        </a>
    <?php endif; ?>
</div>