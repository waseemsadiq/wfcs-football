<div class="bg-surface rounded-md shadow-glow border border-border">
    <div class="p-6 border-b border-border flex justify-between items-center">
        <h1 class="text-2xl font-bold text-text-main m-0">Create
            League</h1>
    </div>

    <div class="p-6">
        <?php if (empty($seasons)): ?>
            <div class="bg-warning/10 text-orange-300 p-4 rounded-sm border border-warning/20 mb-6">
                <p class="mb-4">You need to create a season before you can create a league.</p>
                <a href="<?= $basePath ?>/admin/seasons/create" class="btn btn-primary">Create Season</a>
            </div>
        <?php elseif (empty($teams) || count($teams) < 2): ?>
            <div class="bg-warning/10 text-orange-300 p-4 rounded-sm border border-warning/20 mb-6">
                <p class="mb-4">You need at least 2 teams before you can create a league.</p>
                <a href="<?= $basePath ?>/admin/teams/create" class="btn btn-primary">Add Teams</a>
            </div>
        <?php else: ?>
            <form method="POST" action="<?= $basePath ?>/admin/leagues/store">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div class="mb-6">
                    <label for="name"
                        class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">League Name
                        *</label>
                    <input type="text" id="name" name="name" class="form-input" required aria-required="true"
                        placeholder="e.g. Premier Division">
                </div>

                <div class="mb-6">
                    <label for="seasonId"
                        class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Season *</label>
                    <select id="seasonId" name="seasonId" class="form-input" required aria-required="true">
                        <option value="">Select a season</option>
                        <?php include __DIR__ . '/../partials/season_select.php'; ?>
                    </select>
                </div>

                <?php include __DIR__ . '/../partials/scheduling_fields.php'; ?>

                <?php include __DIR__ . '/../partials/team_selector.php'; ?>

                <div class="flex gap-4 pt-6 border-t border-border">
                    <button type="submit" class="btn btn-primary">Create League</button>
                    <a href="<?= $basePath ?>/admin/leagues" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>