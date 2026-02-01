<?php
/**
 * Render a section header.
 *
 * Variables expected:
 * @var string $title - Section heading text
 * @var string $size - Font size class (default: '2xl')
 */

$size = $size ?? '2xl';
?>

<h2 class="text-<?= $size ?> font-bold mb-6"><?= htmlspecialchars($title) ?></h2>