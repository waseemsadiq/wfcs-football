<div class="">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold m-0 tracking-tight">Edit Cup</h1>
            <p class="text-text-muted mt-1 text-lg"><?= htmlspecialchars($cup['name']) ?></p>
        </div>
        <div class="flex gap-4">
            <a href="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/fixtures"
                class="btn btn-primary">Manage Fixtures</a>
            <a href="<?=$basePath?>/admin/cups" class="btn btn-secondary">Back to Cups</a>
        </div>
    </div>

    <div class="card mb-8">
        <form method="POST" action="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/update">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-text-muted mb-2">Cup Name <span
                        class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-input" required aria-required="true"
                    value="<?= htmlspecialchars($cup['name']) ?>" placeholder="Enter cup name">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pt-6 border-t border-border">
                <div class="col-span-full mb-2">
                    <h3 class="text-lg font-bold text-text-main">Scheduling Defaults</h3>
                    <p class="text-sm text-text-muted">Used when regenerating the remaining bracket fixtures.</p>
                </div>

                <div>
                    <label for="startDate" class="block text-sm font-medium text-text-muted mb-2">First Fixture
                        Date</label>
                    <input type="date" id="startDate" name="startDate"
                        value="<?= htmlspecialchars($cup['startDate'] ?? '') ?>" class="form-input">
                </div>

                <div>
                    <label for="frequency" class="block text-sm font-medium text-text-muted mb-2">Match
                        Frequency</label>
                    <select id="frequency" name="frequency" class="form-input">
                        <option value="weekly" <?= ($cup['frequency'] ?? 'weekly') === 'weekly' ? 'selected' : '' ?>>Weekly
                        </option>
                        <option value="fortnightly" <?= ($cup['frequency'] ?? '') === 'fortnightly' ? 'selected' : '' ?>>
                            Fortnightly</option>
                        <option value="monthly" <?= ($cup['frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly
                        </option>
                    </select>
                </div>

                <div>
                    <label for="matchTime" class="block text-sm font-medium text-text-muted mb-2">Typical Match
                        Time</label>
                    <input type="time" id="matchTime" name="matchTime"
                        value="<?= htmlspecialchars($cup['matchTime'] ?? '15:00') ?>" class="form-input">
                </div>
            </div>

            <div class="flex items-center gap-4 pt-6 border-t border-border">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>"
                    class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class="card border-danger/30">
        <h2 class="text-xl font-bold text-danger mb-4">Danger Zone</h2>
        <p class="text-text-muted mb-6">Permanently delete this cup and all its fixture data.</p>

        <form method="POST" action="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/delete"
            onsubmit="return confirm('Are you sure you want to delete this cup? This cannot be undone.');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <button type="submit"
                class="btn bg-transparent border border-danger text-danger hover:bg-danger hover:text-white">Delete
                Cup</button>
        </form>
    </div>
</div>