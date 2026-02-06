<div class="">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold m-0">Edit <?= htmlspecialchars($player['name']) ?></h1>
        <a href="<?= $basePath ?>/admin/players/<?= htmlspecialchars($player['slug'] ?? $player['id']) ?>"
            class="btn btn-secondary">Cancel</a>
    </div>

    <div class="card">
        <form method="POST"
            action="<?= $basePath ?>/admin/players/<?= htmlspecialchars($player['slug'] ?? $player['id']) ?>/update">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-text-muted mb-2">Player Name <span
                        class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-input" required aria-required="true" autofocus
                    value="<?= htmlspecialchars($player['name']) ?>" placeholder="e.g. James Wilson">
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="isPoolPlayer" name="isPoolPlayer" value="1" class="w-4 h-4 accent-primary"
                        <?= ($player['isPoolPlayer'] ?? false) ? 'checked' : '' ?>
                        onchange="document.getElementById('teamId').disabled = this.checked; document.getElementById('squadNumber').disabled = this.checked; if(this.checked) { document.getElementById('squadNumber').value = ''; }">
                    <span class="text-sm font-medium text-text-muted">Pool Player (no team assignment)</span>
                </label>
                <p class="mt-2 text-sm text-text-muted">Pool players can be assigned to teams when needed.</p>
            </div>

            <div class="mb-6">
                <label for="teamId" class="block text-sm font-medium text-text-muted mb-2">Team</label>
                <select id="teamId" name="teamId" class="form-input"
                    <?= ($player['isPoolPlayer'] ?? false) ? 'disabled' : '' ?>>
                    <option value="">Select a team...</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= htmlspecialchars($team['id']) ?>"
                            <?= ($player['teamId'] == $team['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($team['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-6">
                <label for="position" class="block text-sm font-medium text-text-muted mb-2">Position</label>
                <select id="position" name="position" class="form-input">
                    <option value="">Select position...</option>
                    <option value="Goalkeeper" <?= ($player['position'] === 'Goalkeeper') ? 'selected' : '' ?>>Goalkeeper
                    </option>
                    <option value="Defender" <?= ($player['position'] === 'Defender') ? 'selected' : '' ?>>Defender
                    </option>
                    <option value="Midfielder" <?= ($player['position'] === 'Midfielder') ? 'selected' : '' ?>>Midfielder
                    </option>
                    <option value="Forward" <?= ($player['position'] === 'Forward') ? 'selected' : '' ?>>Forward</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="squadNumber" class="block text-sm font-medium text-text-muted mb-2">Squad Number</label>
                <input type="number" id="squadNumber" name="squadNumber" class="form-input" min="1" max="99"
                    value="<?= htmlspecialchars($player['squadNumber'] ?? '') ?>" placeholder="e.g. 10"
                    <?= ($player['isPoolPlayer'] ?? false) ? 'disabled' : '' ?>>
                <p class="mt-2 text-sm text-text-muted">Squad numbers must be unique within each team (1-99).</p>
            </div>

            <div class="mb-8">
                <label for="status" class="block text-sm font-medium text-text-muted mb-2">Status</label>
                <select id="status" name="status" class="form-input">
                    <option value="active" <?= ($player['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="injured" <?= ($player['status'] === 'injured') ? 'selected' : '' ?>>Injured</option>
                    <option value="suspended" <?= ($player['status'] === 'suspended') ? 'selected' : '' ?>>Suspended
                    </option>
                    <option value="unavailable" <?= ($player['status'] === 'unavailable') ? 'selected' : '' ?>>Unavailable
                    </option>
                </select>
            </div>

            <div class="flex items-center gap-4 pt-6 border-t border-border">
                <button type="submit" class="btn btn-primary">Update Player</button>
                <a href="<?= $basePath ?>/admin/players/<?= htmlspecialchars($player['slug'] ?? $player['id']) ?>"
                    class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
