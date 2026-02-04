<?php
/**
 * Render an empty state message.
 *
 * Variables expected:
 * @var string $message - Empty state message
 * @var string $padding - Padding class (default: 'py-12')
 * @var string|null $actionUrl - Optional action button URL
 * @var string|null $actionText - Optional action button text
 */

$padding = $padding ?? 'py-12';
?>

<div class="text-center <?= $padding ?> px-8">
    <p class="text-text-muted mb-6 text-lg"><?= htmlspecialchars($message) ?></p>
    <?php if (isset($actionUrl) && $actionUrl): ?>
        <a href="<?= htmlspecialchars($actionUrl) ?>" class="btn btn-primary">
            <?= htmlspecialchars($actionText ?? 'Get Started') ?>
        </a>
    <?php endif; ?>
</div>
