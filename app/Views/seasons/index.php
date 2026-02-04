<?php
$title = 'Seasons';
include __DIR__ . '/../partials/admin_page_header.php';
?>

<div class="card">
    <?php
    $title = 'All Seasons';
    $createUrl = $basePath . '/admin/seasons/create';
    $createText = '+ Create Season';
    include __DIR__ . '/../partials/admin_card_header.php';
    ?>

    <?php if (empty($seasons)): ?>
        <?php
        $message = 'No seasons created yet. Create your first season to start organising your leagues and cups.';
        $actionUrl = $basePath . '/admin/seasons/create';
        $actionText = 'Create Your First Season';
        $padding = 'py-16';
        include __DIR__ . '/../partials/empty_state.php';
        ?>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="table-th">Season</th>
                        <th class="table-th">Start Date</th>
                        <th class="table-th">End Date</th>
                        <th class="table-th">Status</th>
                        <th class="table-th text-center">Leagues</th>
                        <th class="table-th text-center">Cups</th>
                        <th class="table-th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($seasons as $season): ?>
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="table-td">
                                <a href="<?= $basePath ?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>"
                                    class="font-bold text-text-main no-underline hover:text-primary transition-colors">
                                    <?= htmlspecialchars($season['name']) ?>
                                </a>
                            </td>
                            <td class="table-td"><?= htmlspecialchars(date('j M Y', strtotime($season['startDate']))) ?></td>
                            <td class="table-td"><?= htmlspecialchars(date('j M Y', strtotime($season['endDate']))) ?></td>
                            <td class="table-td">
                                <?php if (!empty($season['isActive'])): ?>
                                    <span
                                        class="inline-block px-2 py-1 rounded-sm text-xs font-bold uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">Active</span>
                                <?php else: ?>
                                    <span
                                        class="inline-block px-2 py-1 rounded-sm text-xs font-bold uppercase tracking-wider bg-slate-700/50 text-slate-400 border border-slate-700">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="table-td text-center"><?= count($season['leagueIds'] ?? []) ?></td>
                            <td class="table-td text-center"><?= count($season['cupIds'] ?? []) ?></td>
                            <td class="table-td text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <?php if (empty($season['isActive'])): ?>
                                        <form method="POST"
                                            action="<?= $basePath ?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>/set-active"
                                            class="inline-block">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <button type="submit"
                                                class="btn btn-sm border border-primary text-primary hover:bg-primary/10 bg-transparent">Set
                                                Active</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="<?= $basePath ?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>/edit"
                                        class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST"
                                        action="<?= $basePath ?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>/delete"
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this season?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <button type="submit"
                                            class="btn btn-sm border border-danger text-danger hover:bg-danger/10 bg-transparent">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>