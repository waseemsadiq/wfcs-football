<div class="">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h1
            class="text-4xl font-extrabold tracking-tight text-text-main">
            <?= htmlspecialchars($season['name']) ?></h1>
        <div class="flex gap-4">
            <?php if (empty($season['isActive'])): ?>
                <form method="POST"
                    action="<?=$basePath?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>/set-active"
                    class="inline-block">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button type="submit"
                        class="btn border border-primary text-primary hover:bg-primary/10 bg-transparent">Set
                        Active</button>
                </form>
            <?php endif; ?>
            <a href="<?=$basePath?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>/edit"
                class="btn btn-secondary">Edit</a>
            <a href="<?=$basePath?>/admin/seasons" class="btn btn-secondary">Back to Seasons</a>
        </div>
    </div>

    <div class="card mb-8">
        <h2 class="text-xl font-bold mb-6 pb-4 border-b border-border">Season Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-[200px_1fr] gap-y-6 gap-x-8">
            <div class="text-text-muted font-medium">Season ID</div>
            <div class="font-mono text-sm"><?= htmlspecialchars($season['id']) ?></div>

            <div class="text-text-muted font-medium">Status</div>
            <div>
                <?php if (!empty($season['isActive'])): ?>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                        Active Season
                    </span>
                <?php else: ?>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-hover text-text-muted border border-border">
                        Inactive
                    </span>
                <?php endif; ?>
            </div>

            <div class="text-text-muted font-medium">Duration</div>
            <div>
                <?= htmlspecialchars(date('j M Y', strtotime($season['startDate']))) ?>
                <span class="text-text-muted mx-2">to</span>
                <?= htmlspecialchars(date('j M Y', strtotime($season['endDate']))) ?>
            </div>

            <div class="text-text-muted font-medium">Leagues</div>
            <div>
                <?php if (!empty($season['leagueIds'])): ?>
                    <?= count($season['leagueIds']) ?> league(s)
                <?php else: ?>
                    <span class="text-text-muted italic">No leagues added yet</span>
                <?php endif; ?>
            </div>

            <div class="text-text-muted font-medium">Cups</div>
            <div>
                <?php if (!empty($season['cupIds'])): ?>
                    <?= count($season['cupIds']) ?> cup(s)
                <?php else: ?>
                    <span class="text-text-muted italic">No cups added yet</span>
                <?php endif; ?>
            </div>

            <?php if (isset($season['created_at'])): ?>
                <div class="text-text-muted font-medium">Created</div>
                <div class="text-text-muted text-sm">
                    <?= htmlspecialchars(date('j M Y \a\t H:i', strtotime($season['created_at']))) ?></div>
            <?php endif; ?>

            <?php if (isset($season['updated_at'])): ?>
                <div class="text-text-muted font-medium">Last Updated</div>
                <div class="text-text-muted text-sm">
                    <?= htmlspecialchars(date('j M Y \a\t H:i', strtotime($season['updated_at']))) ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>