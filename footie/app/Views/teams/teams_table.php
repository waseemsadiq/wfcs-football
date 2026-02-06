<?php if (empty($teams)): ?>
    <?php
    $message = 'No teams added yet. Get started by adding your first team.';
    $actionUrl = $basePath . '/admin/teams/create';
    $actionText = 'Add Your First Team';
    $padding = 'py-16';
    include __DIR__ . '/../partials/empty_state.php';
    ?>
<?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="table-th">Team</th>
                    <th class="table-th">Contact</th>
                    <th class="table-th">Players</th>
                    <th class="table-th text-right">Actions</th>
                    <th class="table-th w-10 text-center">
                        <input type="checkbox" id="selectAll" title="Select all teams"
                            class="w-4 h-4 accent-primary cursor-pointer">
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team): ?>
                    <tr class="hover:bg-surface-hover transition-colors">
                        <td class="table-td">
                            <div class="flex items-center">
                                <span class="inline-block w-3 h-3 rounded-full mr-3"
                                    style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>;"></span>
                                <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>"
                                    class="font-bold text-text-main no-underline hover:text-primary transition-colors">
                                    <?= htmlspecialchars($team['name']) ?>
                                </a>
                            </div>
                        </td>
                        <td class="table-td">
                            <?php if (!empty($team['contact'])): ?>
                                <?= htmlspecialchars($team['contact']) ?>
                            <?php else: ?>
                                <span class="text-text-muted italic">Not set</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-td"><?= count($team['players'] ?? []) ?></td>
                        <td class="table-td text-right">
                            <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>/edit"
                                class="btn btn-secondary btn-sm mr-2">Edit</a>
                            <form method="POST"
                                action="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>/delete"
                                class="inline-block"
                                onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($team['name'])) ?>? This cannot be undone.');">
                                <input type="hidden" name="csrf_token"
                                    value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
                                <button type="submit"
                                    class="btn btn-sm bg-transparent border border-danger text-danger hover:bg-danger/10">Delete</button>
                            </form>
                        </td>
                        <td class="table-td text-center">
                            <input type="checkbox" name="team_ids[]" value="<?= htmlspecialchars($team['id']) ?>"
                                class="team-checkbox w-4 h-4 accent-primary cursor-pointer">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
    // Include pagination if provided
    if (isset($pagination)):
        include __DIR__ . '/../partials/pagination.php';
    endif;
    ?>
<?php endif; ?>
