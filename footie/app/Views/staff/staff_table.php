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

    <?php
    // Include pagination if provided
    if (isset($pagination)):
        include __DIR__ . '/../partials/pagination.php';
    endif;
    ?>
<?php endif; ?>
