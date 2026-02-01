<?php
/**
 * Render a standard Admin Page Header (centered H1).
 *
 * Variables expected:
 * @var string $title - Page heading text
 */
?>
<div class="text-center mb-12">
    <h1 class="text-4xl font-extrabold tracking-tight mb-2 text-text-main">
        <?= htmlspecialchars($title) ?>
    </h1>
</div>