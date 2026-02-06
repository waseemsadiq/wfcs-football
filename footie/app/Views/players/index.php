<?php
$title = $pool ? 'Pool Players' : 'Players';
include __DIR__ . '/../partials/admin_page_header.php';
?>

<div class="card">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="flex items-center gap-4">
            <h2 class="text-2xl font-bold m-0"><?= $pool ? 'Pool Players' : 'All Players' ?></h2>
            <?php if (!$pool && $selectedTeamId): ?>
                <?php
                $selectedTeam = array_filter($teams, fn($t) => $t['id'] == $selectedTeamId);
                $selectedTeam = reset($selectedTeam);
                ?>
                <?php if ($selectedTeam): ?>
                    <span class="text-text-muted">for <?= htmlspecialchars($selectedTeam['name']) ?></span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="flex gap-4 items-center flex-wrap">
            <select id="teamFilter" class="form-input py-2 px-3 text-sm"
                onchange="window.location.href='<?= $basePath ?>/admin/players?team_id=' + this.value">
                <option value="">All Teams</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= htmlspecialchars($team['id']) ?>"
                        <?= ($selectedTeamId == $team['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($team['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <a href="<?= $basePath ?>/admin/players?pool=1"
                class="btn btn-secondary <?= $pool ? 'bg-primary text-white' : '' ?>">
                Pool Players
            </a>
            <a href="<?= $basePath ?>/admin/players/create" class="btn btn-primary">+ Add Player</a>
            <button type="button" id="deleteSelectedBtn"
                class="btn bg-danger text-white hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                disabled>
                Delete Selected
            </button>
        </div>
    </div>

    <?php if (empty($players)): ?>
        <?php
        $message = $pool ? 'No pool players yet.' : 'No players added yet. Get started by adding your first player.';
        $actionUrl = $basePath . '/admin/players/create';
        $actionText = 'Add Your First Player';
        $padding = 'py-16';
        include __DIR__ . '/../partials/empty_state.php';
        ?>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="table-th">Name</th>
                        <th class="table-th">Team</th>
                        <th class="table-th">Position</th>
                        <th class="table-th text-center">Squad #</th>
                        <th class="table-th">Status</th>
                        <th class="table-th text-center">Goals</th>
                        <th class="table-th text-right">Actions</th>
                        <th class="table-th w-10 text-center">
                            <input type="checkbox" id="selectAll" title="Select all players"
                                class="w-4 h-4 accent-primary cursor-pointer">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $player): ?>
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="table-td">
                                <a href="<?= $basePath ?>/admin/players/<?= htmlspecialchars($player['slug'] ?? $player['id']) ?>"
                                    class="font-bold text-text-main no-underline hover:text-primary transition-colors">
                                    <?= htmlspecialchars($player['name']) ?>
                                </a>
                            </td>
                            <td class="table-td">
                                <?php if (!empty($player['teamId'])): ?>
                                    <?php
                                    $playerTeam = array_filter($teams, fn($t) => $t['id'] == $player['teamId']);
                                    $playerTeam = reset($playerTeam);
                                    ?>
                                    <?php if ($playerTeam): ?>
                                        <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($playerTeam['slug'] ?? $playerTeam['id']) ?>"
                                            class="text-primary hover:underline">
                                            <?= htmlspecialchars($playerTeam['name']) ?>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-text-muted italic">Pool Player</span>
                                <?php endif; ?>
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
                            <td class="table-td text-center">
                                <span class="text-text-muted">0</span>
                            </td>
                            <td class="table-td text-right">
                                <a href="<?= $basePath ?>/admin/players/<?= htmlspecialchars($player['slug'] ?? $player['id']) ?>/edit"
                                    class="btn btn-secondary btn-sm mr-2">Edit</a>
                                <form method="POST"
                                    action="<?= $basePath ?>/admin/players/<?= htmlspecialchars($player['slug'] ?? $player['id']) ?>/delete"
                                    class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($player['name'])) ?>? This cannot be undone.');">
                                    <input type="hidden" name="csrf_token"
                                        value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
                                    <button type="submit"
                                        class="btn btn-sm bg-transparent border border-danger text-danger hover:bg-danger/10">Delete</button>
                                </form>
                            </td>
                            <td class="table-td text-center">
                                <input type="checkbox" name="player_ids[]" value="<?= htmlspecialchars($player['id']) ?>"
                                    class="player-checkbox w-4 h-4 accent-primary cursor-pointer">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Bulk delete form -->
<form id="bulkDeleteForm" method="POST" action="<?= $basePath ?>/admin/players/delete-multiple" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
</form>

<script>
    // Bulk delete functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const playerCheckboxes = document.querySelectorAll('.player-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    // Select all functionality
    selectAllCheckbox?.addEventListener('change', function() {
        playerCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButton();
    });

    // Update delete button state
    playerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDeleteButton);
    });

    function updateDeleteButton() {
        const checkedCount = document.querySelectorAll('.player-checkbox:checked').length;
        deleteSelectedBtn.disabled = checkedCount === 0;
        deleteSelectedBtn.textContent = checkedCount > 0 ?
            `Delete Selected (${checkedCount})` :
            'Delete Selected';
    }

    // Handle bulk delete
    deleteSelectedBtn?.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.player-checkbox:checked');
        const count = checkedBoxes.length;

        if (count === 0) return;

        if (confirm(`Are you sure you want to delete ${count} player${count !== 1 ? 's' : ''}? This cannot be undone.`)) {
            // Add checked IDs to form
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'player_ids[]';
                input.value = checkbox.value;
                bulkDeleteForm.appendChild(input);
            });
            bulkDeleteForm.submit();
        }
    });
</script>
