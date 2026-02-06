<?php
$title = 'Add Staff Member';
include __DIR__ . '/../partials/admin_page_header.php';
?>

<div class="card max-w-2xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-2">Add Staff Member</h2>
        <p class="text-text-muted">Add a coach, manager, or contact person to a team.</p>
    </div>

    <form method="POST" action="<?= $basePath ?>/admin/staff/store" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div>
            <label for="name" class="block text-sm font-bold mb-2">
                Name <span class="text-danger">*</span>
            </label>
            <input type="text" name="name" id="name" required
                class="w-full px-4 py-2 border border-border rounded bg-surface text-text-main focus:outline-none focus:ring-2 focus:ring-primary">
        </div>

        <div>
            <label for="team_id" class="block text-sm font-bold mb-2">
                Team
            </label>
            <select name="team_id" id="team_id"
                class="w-full px-4 py-2 border border-border rounded bg-surface text-text-main focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">None / Global Staff</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= htmlspecialchars($team['id']) ?>" <?= ($selectedTeamId ?? null) == $team['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($team['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="text-sm text-text-muted mt-1">Optional. Leave empty for staff not assigned to a specific team.</p>
        </div>

        <div>
            <label class="block text-sm font-bold mb-3">Role(s) <span class="text-danger">*</span></label>
            <div class="space-y-3">
                <?php foreach ($roles as $value => $label): ?>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative inline-block w-11 h-6">
                            <input type="checkbox" name="roles[]" value="<?= htmlspecialchars($value) ?>"
                                class="sr-only peer" role="switch">
                            <div
                                class="w-11 h-6 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors peer-focus:ring-2 peer-focus:ring-primary/50">
                            </div>
                            <div
                                class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5">
                            </div>
                        </div>
                        <span class="text-sm text-text"><?= htmlspecialchars($label) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <p class="mt-3 text-sm text-text-muted">Select all roles this staff member performs.</p>
        </div>

        <div>
            <label for="email" class="block text-sm font-bold mb-2">Email</label>
            <input type="email" name="email" id="email"
                class="w-full px-4 py-2 border border-border rounded bg-surface text-text-main focus:outline-none focus:ring-2 focus:ring-primary">
            <p class="text-sm text-text-muted mt-1">Optional contact email</p>
        </div>

        <div>
            <label for="phone" class="block text-sm font-bold mb-2">Phone</label>
            <input type="tel" name="phone" id="phone"
                class="w-full px-4 py-2 border border-border rounded bg-surface text-text-main focus:outline-none focus:ring-2 focus:ring-primary">
            <p class="text-sm text-text-muted mt-1">Optional contact phone number</p>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="submit" class="btn btn-primary">
                Add Staff Member
            </button>
            <a href="<?= $basePath ?>/admin/staff" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>