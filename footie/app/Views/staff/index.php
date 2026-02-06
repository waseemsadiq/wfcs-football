<?php
$title = 'Support Staff';
include __DIR__ . '/../partials/admin_page_header.php';
?>

<div class="card">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <h2 class="text-2xl font-bold m-0">Support Staff</h2>
        <div class="flex gap-4 items-center">
            <a href="<?= $basePath ?>/admin/staff/create" class="btn btn-primary">+ Add Staff Member</a>
            <button type="button" id="deleteSelectedBtn"
                class="btn bg-danger text-white hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                disabled>
                Delete Selected
            </button>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <select id="teamFilter"
            class="bg-surface border border-border rounded px-3 py-2 text-text-main focus:outline-none focus:border-primary min-w-[200px]">
            <option value="">All Teams</option>
            <?php foreach ($teams as $team): ?>
                <option value="<?= htmlspecialchars($team['id']) ?>"
                    <?= $selectedTeamId == $team['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($team['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="roleFilter"
            class="bg-surface border border-border rounded px-3 py-2 text-text-main focus:outline-none focus:border-primary min-w-[200px]">
            <option value="">All Roles</option>
            <?php foreach ($roles as $value => $label): ?>
                <option value="<?= htmlspecialchars($value) ?>"
                    <?= $selectedRole === $value ? 'selected' : '' ?>>
                    <?= htmlspecialchars($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="staff-loader" class="hidden flex justify-center py-8" role="status" aria-live="polite">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        <span class="sr-only">Loading staff...</span>
    </div>

    <div id="staff-container">
    <?php if (empty($staff)): ?>
        <?php
        $message = 'No support staff added yet. Add coaches, managers, and contacts to your teams.';
        $actionUrl = $basePath . '/admin/staff/create';
        $actionText = 'Add Your First Staff Member';
        $padding = 'py-16';
        include __DIR__ . '/../partials/empty_state.php';
        ?>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="table-th">Name</th>
                        <th class="table-th">Role</th>
                        <th class="table-th">Team</th>
                        <th class="table-th">Contact</th>
                        <th class="table-th text-right">Actions</th>
                        <th class="table-th w-10 text-center">
                            <input type="checkbox" id="selectAll" title="Select all staff"
                                class="w-4 h-4 accent-primary cursor-pointer">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staff as $member): ?>
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="table-td">
                                <a href="<?= $basePath ?>/admin/staff/<?= htmlspecialchars($member['id']) ?>"
                                    class="font-bold text-text-main no-underline hover:text-primary transition-colors">
                                    <?= htmlspecialchars($member['name']) ?>
                                </a>
                            </td>
                            <td class="table-td">
                                <span class="inline-block px-2 py-1 text-xs rounded bg-primary/10 text-primary">
                                    <?= htmlspecialchars(\App\Models\TeamStaff::formatRole($member['role'])) ?>
                                </span>
                            </td>
                            <td class="table-td">
                                <?php if (!empty($member['team'])): ?>
                                    <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($member['team']['slug']) ?>"
                                        class="text-primary hover:underline">
                                        <?= htmlspecialchars($member['team']['name']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-text-muted italic">No team</span>
                                <?php endif; ?>
                            </td>
                            <td class="table-td">
                                <?php if (!empty($member['email'])): ?>
                                    <a href="mailto:<?= htmlspecialchars($member['email']) ?>"
                                        class="text-primary hover:underline">
                                        <?= htmlspecialchars($member['email']) ?>
                                    </a>
                                <?php elseif (!empty($member['phone'])): ?>
                                    <?= htmlspecialchars($member['phone']) ?>
                                <?php else: ?>
                                    <span class="text-text-muted italic">Not set</span>
                                <?php endif; ?>
                            </td>
                            <td class="table-td text-right">
                                <a href="<?= $basePath ?>/admin/staff/<?= htmlspecialchars($member['id']) ?>/edit"
                                    class="btn btn-secondary btn-sm mr-2">Edit</a>
                                <form method="POST"
                                    action="<?= $basePath ?>/admin/staff/<?= htmlspecialchars($member['id']) ?>/delete"
                                    class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to remove <?= htmlspecialchars(addslashes($member['name'])) ?>? This cannot be undone.');">
                                    <input type="hidden" name="csrf_token"
                                        value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
                                    <button type="submit"
                                        class="btn btn-sm bg-transparent border border-danger text-danger hover:bg-danger/10">Delete</button>
                                </form>
                            </td>
                            <td class="table-td text-center">
                                <input type="checkbox" name="staff_ids[]" value="<?= htmlspecialchars($member['id']) ?>"
                                    class="staff-checkbox w-4 h-4 accent-primary cursor-pointer">
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


    <form id="bulkDeleteForm" method="POST" action="<?= $basePath ?>/admin/staff/delete-multiple" class="hidden">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
    </form>

    <script>
        const BASE_PATH = '<?= $basePath ?>';
        let currentPage = 1;
        let currentTeamId = '<?= $selectedTeamId ?? '' ?>';
        let currentRole = '<?= $selectedRole ?? '' ?>';

        const staffContainer = document.getElementById('staff-container');
        const staffLoader = document.getElementById('staff-loader');
        const teamFilter = document.getElementById('teamFilter');
        const roleFilter = document.getElementById('roleFilter');

        function loadStaff(page = 1) {
            currentPage = page;
            staffLoader.classList.remove('hidden');
            staffContainer.style.opacity = '0.5';

            let url = `${BASE_PATH}/admin/staff/ajax/list?`;
            const params = [];
            if (page > 1) params.push(`page=${page}`);
            if (currentTeamId) params.push(`team_id=${currentTeamId}`);
            if (currentRole) params.push(`role=${currentRole}`);
            url += params.join('&');

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    // Safe to use innerHTML here - content is from our own trusted server endpoint
                    staffContainer.innerHTML = html;
                    initBulkDelete();
                    initPagination();
                })
                .catch(error => {
                    console.error('Error:', error);
                    staffContainer.innerHTML = '<div class="text-error text-center py-8">Failed to load staff.</div>';
                })
                .finally(() => {
                    staffLoader.classList.add('hidden');
                    staffContainer.style.opacity = '1';
                });
        }

        // Filter change handlers
        teamFilter.addEventListener('change', function() {
            currentTeamId = this.value;
            loadStaff(1);
        });

        roleFilter.addEventListener('change', function() {
            currentRole = this.value;
            loadStaff(1);
        });

        // Pagination functionality
        function initPagination() {
            document.querySelectorAll('[data-pagination-prev]').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!this.disabled && currentPage > 1) {
                        loadStaff(currentPage - 1);
                    }
                });
            });

            document.querySelectorAll('[data-pagination-next]').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!this.disabled) {
                        loadStaff(currentPage + 1);
                    }
                });
            });

            document.querySelectorAll('[data-page]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (page !== currentPage) {
                        loadStaff(page);
                    }
                });
            });
        }

        // Bulk delete functionality
        let selectAll, staffCheckboxes, deleteSelectedBtn;
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');

        function initBulkDelete() {
            selectAll = document.getElementById('selectAll');
            staffCheckboxes = document.querySelectorAll('.staff-checkbox');
            deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

            selectAll?.addEventListener('change', function() {
                staffCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateDeleteButton();
            });

            staffCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateDeleteButton);
            });
        }

        function updateDeleteButton() {
            const checkedCount = document.querySelectorAll('.staff-checkbox:checked').length;
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
            const checkedBoxes = document.querySelectorAll('.staff-checkbox:checked');
            const count = checkedBoxes.length;

            if (count === 0) return;

            if (confirm(`Are you sure you want to remove ${count} staff member${count !== 1 ? 's' : ''}? This cannot be undone.`)) {
                checkedBoxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'staff_ids[]';
                    input.value = checkbox.value;
                    bulkDeleteForm.appendChild(input);
                });
                bulkDeleteForm.submit();
            }
        });
    </script>
</div>
