<div class="">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold m-0">Add Player</h1>
        <a href="<?= $basePath ?>/admin/players" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="card">
        <form method="POST" action="<?= $basePath ?>/admin/players/store">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-text-muted mb-2">Player Name <span
                        class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-input" required aria-required="true" autofocus
                    placeholder="e.g. James Wilson">
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="isPoolPlayer" name="isPoolPlayer" value="1" class="w-4 h-4 accent-primary"
                        onchange="document.getElementById('teamId').disabled = this.checked; document.getElementById('squadNumber').disabled = this.checked;">
                    <span class="text-sm font-medium text-text-muted">Pool Player (no team assignment)</span>
                </label>
                <p class="mt-2 text-sm text-text-muted">Pool players can be assigned to teams when needed.</p>
            </div>

            <div class="mb-6">
                <label for="teamId" class="block text-sm font-medium text-text-muted mb-2">Team</label>
                <select id="teamId" name="teamId" class="form-input">
                    <option value="">Select a team...</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= htmlspecialchars($team['id']) ?>">
                            <?= htmlspecialchars($team['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-text-muted mb-3">Position(s)</label>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative inline-block w-11 h-6">
                            <input type="checkbox" name="positions[]" value="Goalkeeper"
                                class="sr-only peer"
                                role="switch">
                            <div class="w-11 h-6 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors peer-focus:ring-2 peer-focus:ring-primary/50"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm text-text">Goalkeeper</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative inline-block w-11 h-6">
                            <input type="checkbox" name="positions[]" value="Defender"
                                class="sr-only peer"
                                role="switch">
                            <div class="w-11 h-6 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors peer-focus:ring-2 peer-focus:ring-primary/50"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm text-text">Defender</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative inline-block w-11 h-6">
                            <input type="checkbox" name="positions[]" value="Midfielder"
                                class="sr-only peer"
                                role="switch">
                            <div class="w-11 h-6 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors peer-focus:ring-2 peer-focus:ring-primary/50"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm text-text">Midfielder</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative inline-block w-11 h-6">
                            <input type="checkbox" name="positions[]" value="Forward"
                                class="sr-only peer"
                                role="switch">
                            <div class="w-11 h-6 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors peer-focus:ring-2 peer-focus:ring-primary/50"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm text-text">Forward</span>
                    </label>
                </div>
                <p class="mt-3 text-sm text-text-muted">Select all positions this player can play.</p>
            </div>

            <div class="mb-6">
                <label for="squadNumber" class="block text-sm font-medium text-text-muted mb-2">Squad Number</label>
                <input type="number" id="squadNumber" name="squadNumber" class="form-input" min="1" max="99"
                    placeholder="e.g. 10">
                <p class="mt-2 text-sm text-text-muted">Squad numbers must be unique within each team (1-99).</p>
            </div>

            <div class="mb-8">
                <label for="status" class="block text-sm font-medium text-text-muted mb-2">Status</label>
                <select id="status" name="status" class="form-input">
                    <option value="active" selected>Active</option>
                    <option value="injured">Injured</option>
                    <option value="suspended">Suspended</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>

            <div class="flex items-center gap-4 pt-6 border-t border-border">
                <button type="submit" class="btn btn-primary">Create Player</button>
                <a href="<?= $basePath ?>/admin/players" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
