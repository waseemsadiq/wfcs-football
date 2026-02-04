<?php
$title = 'Leagues';
include __DIR__ . '/../partials/admin_page_header.php';
?>

<div class="card">
    <?php
    $title = 'All Leagues';
    $createUrl = $basePath . '/admin/leagues/create';
    $createText = '+ Create League';
    include __DIR__ . '/../partials/admin_card_header.php';
    ?>

    <?php if (empty($leagues)): ?>
        <?php
        $message = 'No leagues created yet. Create your first league to start organising fixtures.';
        $actionUrl = $basePath . '/admin/leagues/create';
        $actionText = 'Create Your First League';
        $padding = 'py-16';
        include __DIR__ . '/../partials/empty_state.php';
        ?>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="table-th">League</th>
                        <th class="table-th">Season</th>
                        <th class="table-th">Teams</th>
                        <th class="table-th">Fixtures</th>
                        <th class="table-th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leagues as $league): ?>
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="table-td">
                                <a href="<?= $basePath ?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
                                    class="font-bold text-text-main no-underline hover:text-primary transition-colors">
                                    <?= htmlspecialchars($league['name']) ?>
                                </a>
                            </td>
                            <td class="table-td"><span
                                    class="text-text-muted"><?= htmlspecialchars($league['seasonName'] ?? '') ?></span></td>
                            <td class="table-td"><?= count($league['teamIds'] ?? []) ?></td>
                            <td class="table-td"><?= count($league['fixtures'] ?? []) ?></td>
                            <td class="table-td text-right">
                                <a href="<?= $basePath ?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
                                    class="btn btn-secondary btn-sm mr-2">View</a>
                                <a href="<?= $basePath ?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/fixtures"
                                    class="btn btn-secondary btn-sm">Fixtures</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>