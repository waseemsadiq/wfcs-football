<div class="">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h1
            class="text-4xl font-extrabold tracking-tight text-text-main flex items-center">
            <span class="inline-block w-6 h-6 rounded-sm mr-4 shadow-sm"
                style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>;"></span>
            <?= htmlspecialchars($team['name']) ?>
        </h1>
        <div class="flex gap-4">
            <a href="<?=$basePath?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>/edit"
                class="btn btn-secondary">Edit Team</a>
            <a href="<?=$basePath?>/admin/teams" class="btn btn-secondary">Back to Teams</a>
        </div>
    </div>

    <!-- Team Details -->
    <div class="card mb-8">
        <h2 class="text-xl font-bold mb-6 pb-4 border-b border-border">Team Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-[200px_1fr] gap-y-6 gap-x-8">
            <div class="text-text-muted font-medium">Contact Person</div>
            <div>
                <?php if (!empty($team['contact'])): ?>
                    <?= htmlspecialchars($team['contact']) ?>
                <?php else: ?>
                    <span class="text-text-muted italic">Not set</span>
                <?php endif; ?>
            </div>

            <div class="text-text-muted font-medium">Email</div>
            <div>
                <?php if (!empty($team['email'])): ?>
                    <a href="mailto:<?= htmlspecialchars($team['email']) ?>"
                        class="text-primary hover:text-primary-hover underline decoration-primary/30 underline-offset-4 transition-colors"><?= htmlspecialchars($team['email']) ?></a>
                <?php else: ?>
                    <span class="text-text-muted italic">Not set</span>
                <?php endif; ?>
            </div>

            <div class="text-text-muted font-medium">Team Colour</div>
            <div class="flex items-center gap-3">
                <span class="inline-block w-6 h-6 rounded border border-border"
                    style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>"></span>
                <span class="font-mono text-sm"><?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?></span>
            </div>
        </div>
    </div>

    <!-- Squad -->
    <div class="card mb-8">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-border">
            <h2 class="text-xl font-bold m-0">Squad</h2>
            <span
                class="px-3 py-1 bg-surface-hover rounded-full text-xs font-bold text-text-muted uppercase tracking-wider">
                <?= count($team['players'] ?? []) ?> player<?= count($team['players'] ?? []) !== 1 ? 's' : '' ?>
            </span>
        </div>

        <?php if (empty($team['players'])): ?>
            <div class="text-center py-12 text-text-muted">
                <p class="mb-6">No players added to this team yet.</p>
                <a href="<?=$basePath?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>/edit"
                    class="btn btn-primary">Add Players</a>
            </div>
        <?php else: ?>
            <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($team['players'] as $index => $player): ?>
                    <li
                        class="flex items-center p-3 bg-surface-hover/30 rounded border border-border hover:border-primary/50 transition-colors">
                        <span
                            class="flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mr-3">
                            <?= $index + 1 ?>
                        </span>
                        <span class="font-medium"><?= htmlspecialchars($player) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Competitions -->
    <div class="card mb-8">
        <h2 class="text-xl font-bold mb-6 pb-4 border-b border-border">Competitions</h2>

        <?php if (empty($leagues) && empty($cups)): ?>
            <div class="text-center py-12 text-text-muted">
                <p>This team is not currently participating in any competitions.</p>
            </div>
        <?php else: ?>
            <?php if (!empty($leagues)): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-text-muted uppercase tracking-wide text-sm">Leagues</h3>
                    <ul class="space-y-3">
                        <?php foreach ($leagues as $league): ?>
                            <li class="flex items-center justify-between p-4 bg-surface-hover/30 rounded border border-border hover:border-primary/50 transition-colors">
                                <a href="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
                                    class="text-lg font-medium hover:text-primary transition-colors flex-1">
                                    <?= htmlspecialchars($league['name']) ?>
                                </a>
                                <span class="text-xs text-text-muted bg-surface-hover px-3 py-1 rounded-full">League</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($cups)): ?>
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-text-muted uppercase tracking-wide text-sm">Cups</h3>
                    <ul class="space-y-3">
                        <?php foreach ($cups as $cup): ?>
                            <li class="flex items-center justify-between p-4 bg-surface-hover/30 rounded border border-border hover:border-primary/50 transition-colors">
                                <a href="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>"
                                    class="text-lg font-medium hover:text-primary transition-colors flex-1">
                                    <?= htmlspecialchars($cup['name']) ?>
                                </a>
                                <span class="text-xs text-text-muted bg-surface-hover px-3 py-1 rounded-full">Cup</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Danger Zone -->
    <div class="card border-danger/30">
        <h2 class="text-xl font-bold text-danger mb-4">Danger Zone</h2>
        <p class="text-text-muted mb-6">Removing this team will not affect past results or standings, but the team will
            no longer appear in lists or be available for new fixtures.</p>
        <form method="POST" action="<?=$basePath?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>/delete"
            onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($team['name'])) ?>? This cannot be undone.');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
            <button type="submit"
                class="btn bg-transparent border border-danger text-danger hover:bg-danger hover:text-white">Delete
                Team</button>
        </form>
    </div>
</div>