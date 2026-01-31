<div class="bg-surface w-full max-w-md p-10 rounded-md shadow-glow border border-border">
    <div class="flex items-center justify-center gap-4 mb-2">
        <img src="<?= $basePath ?>/images/logo-white.svg" alt="WFCS Football Logo" class="h-12 w-12">
        <h1 class="text-3xl font-bold">WFCS Football</h1>
    </div>
    <p class="text-center text-text-muted mb-8">Enter the admin password to continue</p>

    <?php if (isset($error) && $error): ?>
        <div class="bg-danger/10 text-red-300 p-4 rounded-sm border border-danger/20 mb-6">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $basePath ?>/login" class="flex flex-col gap-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
        <div>
            <label for="password"
                class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Password</label>
            <input type="password" id="password" name="password" required autofocus placeholder="Enter admin password"
                class="form-input">
        </div>

        <button type="submit" class="btn btn-primary w-full">Log in</button>
    </form>
</div>