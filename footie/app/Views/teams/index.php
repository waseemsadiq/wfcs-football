<?php
$title = 'Teams';
include __DIR__ . '/../partials/admin_page_header.php';
?>

<div class="card">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h2 class="text-2xl font-bold m-0">All Teams</h2>
        <div class="flex gap-4 items-center">
            <a href="<?= $basePath ?>/admin/teams/create" class="btn btn-primary">+ Add Team</a>
            <button type="button" id="deleteSelectedBtn"
                class="btn bg-danger text-white hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                disabled>
                Delete Selected
            </button>
        </div>
    </div>

    <div id="teams-loader" class="hidden flex justify-center py-8" role="status" aria-live="polite">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        <span class="sr-only">Loading teams...</span>
    </div>

    <div id="teams-container">
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
    <?php endif; ?>

    <?php
    // Include pagination
    if (isset($pagination) && $pagination['totalPages'] > 1):
        include __DIR__ . '/../partials/pagination.php';
    endif;
    ?>
    </div>


    <form id="bulkDeleteForm" method="POST" action="<?= $basePath ?>/admin/teams/delete-multiple" class="hidden">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
    </form>

    <script>
        const BASE_PATH = '<?= $basePath ?>';
        let currentPage = 1;

        const teamsContainer = document.getElementById('teams-container');
        const teamsLoader = document.getElementById('teams-loader');

        function loadTeams(page = 1) {
            currentPage = page;
            teamsLoader.classList.remove('hidden');
            teamsContainer.style.opacity = '0.5';

            let url = `${BASE_PATH}/admin/teams/ajax/list?`;
            if (page > 1) {
                url += `page=${page}`;
            }

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    // Safe to use innerHTML here - content is from our own trusted server endpoint
                    teamsContainer.innerHTML = html;

                    // Reinitialize bulk delete and pagination after loading new content
                    initBulkDelete();
                    initPagination();
                })
                .catch(error => {
                    console.error('Error:', error);
                    teamsContainer.innerHTML = '<div class="text-error text-center py-8">Failed to load teams.</div>';
                })
                .finally(() => {
                    teamsLoader.classList.add('hidden');
                    teamsContainer.style.opacity = '1';
                });
        }

        // Pagination functionality
        function initPagination() {
            // Handle pagination button clicks
            document.querySelectorAll('[data-pagination-prev]').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!this.disabled && currentPage > 1) {
                        loadTeams(currentPage - 1);
                    }
                });
            });

            document.querySelectorAll('[data-pagination-next]').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!this.disabled) {
                        loadTeams(currentPage + 1);
                    }
                });
            });

            document.querySelectorAll('[data-page]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (page !== currentPage) {
                        loadTeams(page);
                    }
                });
            });
        }

        // Bulk delete functionality
        let selectAll, teamCheckboxes, deleteSelectedBtn;
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');

        function initBulkDelete() {
            selectAll = document.getElementById('selectAll');
            teamCheckboxes = document.querySelectorAll('.team-checkbox');
            deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

            // Select all functionality
            selectAll?.addEventListener('change', function() {
                teamCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateDeleteButton();
            });

            // Update delete button state
            teamCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateDeleteButton);
            });
        }

        function updateDeleteButton() {
            const checkedCount = document.querySelectorAll('.team-checkbox:checked').length;
            deleteSelectedBtn.disabled = checkedCount === 0;
            deleteSelectedBtn.textContent = checkedCount > 0 ?
                `Delete Selected (${checkedCount})` :
                'Delete Selected';
        }

        // Initialize on page load
        initBulkDelete();
        initPagination();

        // Handle bulk delete
        deleteSelectedBtn?.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.team-checkbox:checked');
            const count = checkedBoxes.length;

            if (count === 0) return;

            if (confirm(`Are you sure you want to delete ${count} team${count !== 1 ? 's' : ''}? This cannot be undone.`)) {
                // Add checked IDs to form
                checkedBoxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'team_ids[]';
                    input.value = checkbox.value;
                    bulkDeleteForm.appendChild(input);
                });
                bulkDeleteForm.submit();
            }
        });
    </script>
</div>