<?php
$title = $staff['name'];
include __DIR__ . '/../partials/admin_page_header.php';
?>

<div class="card max-w-4xl mx-auto">
    <div class="flex justify-between items-start mb-8">
        <div>
            <h2 class="text-3xl font-bold mb-2"><?= htmlspecialchars($staff['name']) ?></h2>
            <p class="text-text-muted">
                <span class="inline-block px-2 py-1 text-xs rounded bg-primary/10 text-primary">
                    <?= htmlspecialchars(\App\Models\TeamStaff::formatRole($staff['role'])) ?>
                </span>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="<?= $basePath ?>/admin/staff/<?= htmlspecialchars($staff['id']) ?>/edit" class="btn btn-secondary">
                Edit
            </a>
            <form method="POST" action="<?= $basePath ?>/admin/staff/<?= htmlspecialchars($staff['id']) ?>/delete"
                class="inline-block"
                onsubmit="return confirm('Are you sure you want to remove <?= htmlspecialchars(addslashes($staff['name'])) ?>? This cannot be undone.');">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>">
                <button type="submit" class="btn bg-transparent border border-danger text-danger hover:bg-danger/10">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-bold text-text-muted mb-1">Team</h3>
                <?php if (!empty($staff['teamName'])): ?>
                    <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($staff['teamSlug']) ?>"
                        class="text-primary hover:underline text-lg">
                        <?= htmlspecialchars($staff['teamName']) ?>
                    </a>
                <?php else: ?>
                    <p class="text-text-muted italic">None / Global Staff</p>
                <?php endif; ?>
            </div>

            <div>
                <h3 class="text-sm font-bold text-text-muted mb-1">Role(s)</h3>
                <p class="text-lg"><?= htmlspecialchars(\App\Models\TeamStaff::formatRole($staff['role'])) ?></p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-bold text-text-muted mb-1">Email</h3>
                <?php if (!empty($staff['email'])): ?>
                    <a href="mailto:<?= htmlspecialchars($staff['email']) ?>" class="text-primary hover:underline text-lg">
                        <?= htmlspecialchars($staff['email']) ?>
                    </a>
                <?php else: ?>
                    <p class="text-text-muted italic">Not set</p>
                <?php endif; ?>
            </div>

            <div>
                <h3 class="text-sm font-bold text-text-muted mb-1">Phone</h3>
                <?php if (!empty($staff['phone'])): ?>
                    <a href="tel:<?= htmlspecialchars($staff['phone']) ?>" class="text-primary hover:underline text-lg">
                        <?= htmlspecialchars($staff['phone']) ?>
                    </a>
                <?php else: ?>
                    <p class="text-text-muted italic">Not set</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mt-8 pt-8 border-t border-border">
        <div class="flex justify-between items-center">
            <div class="text-sm text-text-muted">
                <p>Added: <?= date('d M Y', strtotime($staff['createdAt'])) ?></p>
                <?php if ($staff['updatedAt'] !== $staff['createdAt']): ?>
                    <p>Updated: <?= date('d M Y', strtotime($staff['updatedAt'])) ?></p>
                <?php endif; ?>
            </div>
            <a href="<?= $basePath ?>/admin/staff" class="btn btn-secondary">
                Back to Staff List
            </a>
        </div>
    </div>
</div>