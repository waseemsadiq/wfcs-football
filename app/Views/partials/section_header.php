<?php
/**
 * Render a section header with optional divider.
 *
 * Variables expected:
 * @var string $title - Section heading text
 * @var string $size - Font size class (default: '2xl')
 * @var bool $showDivider - Show horizontal line (default: true)
 */

$size = $size ?? '2xl';
$showDivider = $showDivider ?? true;
?>

<div class="flex items-center gap-4 mb-6">
    <h2 class="text-<?= $size ?> font-bold"><?= htmlspecialchars($title) ?></h2>
    <?php if ($showDivider): ?>
        <div class="h-px bg-border flex-1"></div>
    <?php endif; ?>
</div>
