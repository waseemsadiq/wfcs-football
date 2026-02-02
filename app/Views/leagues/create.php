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
                    <input type="text" id="name" name="name" class="form-input" required
                        placeholder="e.g. Premier Division">
                </div>

                <div class="mb-6">
                    <label for="seasonId"
                        class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Season *</label>
                    <select id="seasonId" name="seasonId" class="form-input" required>
                        <option value="">Select a season</option>
                        <?php foreach ($seasons as $season): ?>
                            <option value="<?= htmlspecialchars($season['id']) ?>">
                                <?= htmlspecialchars($season['name']) ?>
                                <?= $season['isActive'] ? '(Active)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="startDate"
                            class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">First Fixture
                            Date *</label>
                        <input type="date" id="startDate" name="startDate" class="form-input" required>
                    </div>

                    <div>
                        <label for="frequency"
                            class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Match Frequency
                            *</label>
                        <select id="frequency" name="frequency" class="form-input" required>
                            <option value="weekly">Weekly</option>
                            <option value="fortnightly">Fortnightly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div>
                        <label for="matchTime"
                            class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Typical Match
                            Time *</label>
                        <input type="time" id="matchTime" name="matchTime" class="form-input" required value="15:00">
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Select Teams *
                        (minimum 2)</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4">
                        <?php foreach ($teams as $team): ?>
                            <label
                                class="flex items-center gap-3 p-3 bg-background rounded-sm cursor-pointer border border-transparent hover:border-primary/50 transition-colors">
                                <input type="checkbox" name="teamIds[]" value="<?= htmlspecialchars($team['id']) ?>"
                                    class="accent-primary w-5 h-5">
                                <span class="inline-block w-3 h-3 rounded-full"
                                    style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>;"></span>
                                <span class="text-text-main font-medium"><?= htmlspecialchars($team['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="flex gap-4 pt-6 border-t border-border">
                    <button type="submit" class="btn btn-primary">Create League</button>
                    <a href="<?= $basePath ?>/admin/leagues" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>