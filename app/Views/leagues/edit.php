<div class="text-center mb-12">
    <h1
        class="text-4xl font-extrabold tracking-tight mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">
        Edit League</h1>
    <h2 class="text-2xl font-bold mb-6 text-text-muted"><?= htmlspecialchars($league['name']) ?></h2>
</div>

<div class="card mb-8">
    <form method="POST" action="/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/update">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <div class="mb-8">
            <label for="name" class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">League
                Name *</label>
            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($league['name']) ?>"
                placeholder="Enter league name" class="form-input">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div>
                <label for="startDate"
                    class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">First Fixture
                    Date</label>
                <input type="date" id="startDate" name="startDate"
                    value="<?= htmlspecialchars($league['startDate'] ?? '') ?>" class="form-input">
            </div>

            <div>
                <label for="frequency"
                    class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Match
                    Frequency</label>
                <select id="frequency" name="frequency" class="form-input">
                    <option value="weekly" <?= ($league['frequency'] ?? 'weekly') === 'weekly' ? 'selected' : '' ?>>Weekly
                    </option>
                    <option value="fortnightly" <?= ($league['frequency'] ?? '') === 'fortnightly' ? 'selected' : '' ?>>
                        Fortnightly</option>
                    <option value="monthly" <?= ($league['frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly
                    </option>
                </select>
            </div>

            <div>
                <label for="matchTime"
                    class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Typical Match
                    Time</label>
                <input type="time" id="matchTime" name="matchTime"
                    value="<?= htmlspecialchars($league['matchTime'] ?? '15:00') ?>" class="form-input">
            </div>
        </div>

        <div class="flex gap-4 mt-8 border-t border-border pt-8">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
                class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<div class="card border-danger/50 mt-8">
    <h2 class="text-danger text-2xl font-bold mb-4">Danger Zone</h2>
    <p class="text-text-muted mb-6">Permanently delete this league and all its fixture data.</p>

    <form method="POST" action="/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/delete"
        onsubmit="return confirm('Are you sure you want to delete this league? This cannot be undone.');">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <button type="submit" class="btn bg-transparent border border-danger text-danger hover:bg-danger/10">Delete
            League</button>
    </form>
</div>