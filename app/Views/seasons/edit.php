<div class="">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold m-0">Edit <?= htmlspecialchars($season['name']) ?></h1>
        <a href="<?=$basePath?>/admin/seasons" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="card">
        <form method="POST" action="<?=$basePath?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>/update">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-6">
                <label for="id" class="block text-sm font-medium text-text-muted mb-2">Season ID</label>
                <input type="text" id="id" class="form-input bg-surface-hover opacity-75 cursor-not-allowed"
                    value="<?= htmlspecialchars($season['id']) ?>" disabled>
                <p class="mt-2 text-sm text-text-muted">The season ID cannot be changed after creation.</p>
            </div>

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-text-muted mb-2">Season Name</label>
                <input type="text" id="name" name="name" class="form-input" required aria-required="true"
                    value="<?= htmlspecialchars($season['name']) ?>" placeholder="e.g. 2024/25 Season">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="startDate" class="block text-sm font-medium text-text-muted mb-2">Start Date</label>
                    <input type="date" id="startDate" name="startDate" class="form-input" required aria-required="true"
                        value="<?= htmlspecialchars($season['startDate']) ?>">
                </div>
                <div>
                    <label for="endDate" class="block text-sm font-medium text-text-muted mb-2">End Date</label>
                    <input type="date" id="endDate" name="endDate" class="form-input" required aria-required="true"
                        value="<?= htmlspecialchars($season['endDate']) ?>">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-text-muted mb-2">Status</label>
                <?php if (!empty($season['isActive'])): ?>
                    <div class="p-3 bg-blue-500/10 border border-blue-500/30 rounded-sm inline-block">
                        <span class="text-blue-400 font-bold uppercase tracking-wider text-sm flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Active Season
                        </span>
                    </div>
                <?php else: ?>
                    <div class="p-3 bg-surface-hover border border-border rounded-sm inline-block">
                        <span class="text-text-muted font-bold uppercase tracking-wider text-sm">Inactive</span>
                    </div>
                    <p class="mt-2 text-sm text-text-muted">You can set this season as active from the seasons list.</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($season['leagueIds']) || !empty($season['cupIds'])): ?>
                <div class="mb-8 p-4 bg-surface-hover/30 rounded border border-border border-dashed">
                    <label class="block text-sm font-bold text-text-main mb-2">Competitions</label>
                    <p class="text-sm text-text-muted">
                        This season has <strong class="text-text-main"><?= count($season['leagueIds'] ?? []) ?></strong>
                        league(s) and
                        <strong class="text-text-main"><?= count($season['cupIds'] ?? []) ?></strong> cup(s).
                    </p>
                </div>
            <?php endif; ?>

            <div class="flex items-center gap-4 pt-6 border-t border-border">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?=$basePath?>/admin/seasons" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>