<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-6">
    <div class="text-6xl mb-6 opacity-50">🔍</div>
    <h1 class="text-4xl font-bold mb-4 text-text-main">Not Found
    </h1>
    <p class="text-text-muted text-lg mb-8 max-w-md mx-auto">
        <?= htmlspecialchars($message ?? 'The page you are looking for does not exist.') ?>
    </p>
    <a href="<?= $basePath ?>/" class="btn btn-primary">Back to home</a>
</div>