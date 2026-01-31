<div class="text-center mb-12">
    <h1
        class="text-4xl font-extrabold tracking-tight mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">
        Teams</h1>
</div>

<div class="card">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h2 class="text-2xl font-bold m-0">All Teams</h2>
        <div class="flex gap-4 items-center">
            <a href="<?=$basePath?>/admin/teams/create" class="btn btn-primary">+ Add Team</a>
            <button type="button" id="deleteSelectedBtn"
                class="btn bg-danger text-white hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                disabled>
                Delete Selected
            </button>
        </div>
    </div>

    <?php if (empty($teams)): ?>
        <div class="text-center py-16 px-8">
            <p class="text-text-muted mb-6 text-lg">No teams added yet.</p>
            <p class="text-text-muted mb-8">Get started by adding your first team.</p>
            <a href="<?=$basePath?>/admin/teams/create" class="btn btn-primary">Add Your First Team</a>
        </div>
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
                                    <span class="inline-block w-3 h-3 rounded-sm mr-3"
                                        style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>;"></span>
                                    <a href="<?=$basePath?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>"
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
                                <a href="<?=$basePath?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>/edit"
                                    class="btn btn-secondary btn-sm mr-2">Edit</a>
                                <form method="POST"
                                    action="<?=$basePath?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>/delete"
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
    <?php endif; ?>

    <form id="bulkDeleteForm" method="POST" action="<?=$basePath?>/admin/teams/delete-multiple" class="hidden">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
            const checkboxes = document.querySelectorAll('.team-checkbox');

            function updateToolbar() {
                const checkedCount = document.querySelectorAll('.team-checkbox:checked').length;
                if (checkedCount > 0) {
                    deleteSelectedBtn.removeAttribute('disabled');
                    deleteSelectedBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    deleteSelectedBtn.setAttribute('disabled', 'disabled');
                    deleteSelectedBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function (e) {
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = e.target.checked;
                    });
                    updateToolbar();
                });
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const allCheckboxes = document.querySelectorAll('.team-checkbox');
                    const checkedCount = document.querySelectorAll('.team-checkbox:checked').length;

                    if (selectAll) {
                        selectAll.checked = checkedCount === allCheckboxes.length;
                        selectAll.indeterminate = checkedCount > 0 && checkedCount < allCheckboxes.length;
                    }
                    updateToolbar();
                });
            });

            if (deleteSelectedBtn) {
                deleteSelectedBtn.addEventListener('click', function () {
                    const checkedCount = document.querySelectorAll('.team-checkbox:checked').length;
                    if (confirm('Are you sure you want to delete ' + checkedCount + ' team' + (checkedCount !== 1 ? 's' : '') + '? This cannot be undone.')) {
                        const form = document.getElementById('bulkDeleteForm');
                        const csrfToken = form.querySelector('input[name="csrf_token"]').value;

                        // Clear form but keep csrf
                        form.innerHTML = '';
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = 'csrf_token';
                        csrfInput.value = csrfToken;
                        form.appendChild(csrfInput);

                        // Add selected IDs
                        document.querySelectorAll('.team-checkbox:checked').forEach(checkbox => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'team_ids[]';
                            input.value = checkbox.value;
                            form.appendChild(input);
                        });

                        form.submit();
                    }
                });
            }
        });
    </script>
</div>