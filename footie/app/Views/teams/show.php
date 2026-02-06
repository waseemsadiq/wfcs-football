<div class="">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h1 class="text-4xl font-extrabold tracking-tight text-text-main flex items-center">
            <span class="inline-block w-6 h-6 rounded-full mr-4 shadow-sm"
                style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>;"></span>
            <?= htmlspecialchars($team['name']) ?>
        </h1>
        <div class="flex gap-4">
            <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>/edit"
                class="btn btn-secondary">Edit Team</a>
            <a href="<?= $basePath ?>/admin/teams" class="btn btn-secondary">Back to Teams</a>
        </div>
    </div>

    <!-- Team Details -->
    <div class="card mb-8">
        <h2 class="text-xl font-bold mb-6 pb-4 border-b border-border">Team Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-[200px_1fr] gap-y-6 gap-x-8">
            <div class="text-text-muted font-medium">Team Colour</div>
            <div class="flex items-center gap-3">
                <span class="inline-block w-6 h-6 rounded-full border border-border"
                    style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>"></span>
                <span class="font-mono text-sm"><?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?></span>
            </div>
        </div>
    </div>

    <!-- Squad -->
    <div class="card mb-8">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-border">
            <h2 class="text-xl font-bold m-0">Squad</h2>
            <div class="flex items-center gap-4">
                <span
                    class="px-3 py-1 bg-surface-hover rounded-full text-xs font-bold text-text-muted uppercase tracking-wider">
                    <?php
                    $playerModel = new \App\Models\Player();
                    $squadPlayers = $playerModel->getByTeam($team['id']);
                    ?>
                    <?= count($squadPlayers) ?> player<?= count($squadPlayers) !== 1 ? 's' : '' ?>
                </span>
                <a href="<?= $basePath ?>/admin/players?team_id=<?= htmlspecialchars($team['id']) ?>"
                    class="btn btn-secondary btn-sm">Manage Squad</a>
            </div>
        </div>

        <?php if (empty($squadPlayers)): ?>
            <div class="text-center py-12 text-text-muted">
                <p class="mb-6">No players added to this team yet.</p>
                <a href="<?= $basePath ?>/admin/players/create"
                    class="btn btn-primary">Add First Player</a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="table-th text-left">Name</th>
                            <th class="table-th text-left">Position</th>
                            <th class="table-th text-center">#</th>
                            <th class="table-th text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($squadPlayers as $player): ?>
                            <tr class="hover:bg-surface-hover transition-colors">
                                <td class="table-td">
                                    <a href="<?= $basePath ?>/admin/players/<?= htmlspecialchars($player['slug'] ?? $player['id']) ?>"
                                        class="font-medium hover:text-primary transition-colors">
                                        <?= htmlspecialchars($player['name']) ?>
                                    </a>
                                </td>
                                <td class="table-td">
                                    <?php if (!empty($player['position'])): ?>
                                        <span class="text-xs px-2 py-1 rounded bg-surface-hover">
                                            <?= htmlspecialchars($player['position']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-text-muted italic">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="table-td text-center">
                                    <?php if (!empty($player['squadNumber'])): ?>
                                        <span class="font-mono font-bold"><?= htmlspecialchars($player['squadNumber']) ?></span>
                                    <?php else: ?>
                                        <span class="text-text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="table-td">
                                    <?php
                                    $statusColors = [
                                        'active' => 'bg-green-500/20 text-green-400',
                                        'injured' => 'bg-red-500/20 text-red-400',
                                        'suspended' => 'bg-yellow-500/20 text-yellow-400',
                                        'unavailable' => 'bg-gray-500/20 text-gray-400',
                                    ];
                                    $statusColor = $statusColors[$player['status']] ?? 'bg-gray-500/20 text-gray-400';
                                    ?>
                                    <span class="text-xs px-2 py-1 rounded <?= $statusColor ?>">
                                        <?= htmlspecialchars(ucfirst($player['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Support Staff -->
    <div class="card mb-8">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-border">
            <h2 class="text-xl font-bold m-0">Support Staff</h2>
            <div class="flex items-center gap-4">
                <span
                    class="px-3 py-1 bg-surface-hover rounded-full text-xs font-bold text-text-muted uppercase tracking-wider">
                    <?= count($staff) ?> staff member<?= count($staff) !== 1 ? 's' : '' ?>
                </span>
                <a href="<?= $basePath ?>/admin/staff/create?team_id=<?= htmlspecialchars($team['id']) ?>"
                    class="btn btn-secondary btn-sm">Add Staff</a>
            </div>
        </div>

        <?php if (empty($staff)): ?>
            <div class="text-center py-12 text-text-muted">
                <p class="mb-6">No support staff added to this team yet.</p>
                <a href="<?= $basePath ?>/admin/staff/create?team_id=<?= htmlspecialchars($team['id']) ?>"
                    class="btn btn-primary">Add First Staff Member</a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="table-th text-left">Name</th>
                            <th class="table-th text-left">Role</th>
                            <th class="table-th text-left">Contact</th>
                            <th class="table-th text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staff as $member): ?>
                            <tr class="hover:bg-surface-hover transition-colors">
                                <td class="table-td">
                                    <a href="<?= $basePath ?>/admin/staff/<?= htmlspecialchars($member['id']) ?>"
                                        class="font-medium hover:text-primary transition-colors">
                                        <?= htmlspecialchars($member['name']) ?>
                                    </a>
                                </td>
                                <td class="table-td">
                                    <span class="inline-block px-2 py-1 text-xs rounded bg-primary/10 text-primary">
                                        <?= htmlspecialchars(\App\Models\TeamStaff::formatRole($member['role'])) ?>
                                    </span>
                                </td>
                                <td class="table-td">
                                    <?php if (!empty($member['email'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($member['email']) ?>"
                                            class="text-primary hover:underline">
                                            <?= htmlspecialchars($member['email']) ?>
                                        </a>
                                    <?php elseif (!empty($member['phone'])): ?>
                                        <a href="tel:<?= htmlspecialchars($member['phone']) ?>"
                                            class="text-primary hover:underline">
                                            <?= htmlspecialchars($member['phone']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-text-muted italic">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td class="table-td text-right">
                                    <a href="<?= $basePath ?>/admin/staff/<?= htmlspecialchars($member['id']) ?>/edit"
                                        class="btn btn-secondary btn-sm">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
                            <li
                                class="flex items-center justify-between p-4 bg-surface-hover/30 rounded border border-border hover:border-primary/50 transition-colors">
                                <a href="<?= $basePath ?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
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
                            <li
                                class="flex items-center justify-between p-4 bg-surface-hover/30 rounded border border-border hover:border-primary/50 transition-colors">
                                <a href="<?= $basePath ?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>"
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
        <form method="POST"
            action="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>/delete"
            onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($team['name'])) ?>? This cannot be undone.');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
            <button type="submit"
                class="btn bg-transparent border border-danger text-danger hover:bg-danger hover:text-white">Delete
                Team</button>
        </form>
    </div>
</div>