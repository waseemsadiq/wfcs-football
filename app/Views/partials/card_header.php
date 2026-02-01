<?php
/**
 * Render a card header with title inside.
 *
 * Variables expected:
 * @var string $title - Section heading text
 */
?>

<div class="p-6 border-b border-border bg-surface/50">
    <h2 class="text-xl font-bold m-0">
        <?= htmlspecialchars($title) ?>
    </h2>
</div>