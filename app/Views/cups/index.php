<?php
$title = 'Cups';
include __DIR__ . '/../partials/admin_page_header.php';
?>

<div class="card">
    <?php
    $title = 'All Cups';
    $createUrl = $basePath . '/admin/cups/create';
    $createText = '+ Create Cup';
    include __DIR__ . '/../partials/admin_card_header.php';
    ?>

    <?php if (empty($cups)): ?>
        <?php
        $message = 'Cup competitions allow you to create knockout tournaments. Create your first cup to generate a bracket and fixtures.';
        $actionUrl = $basePath . '/admin/cups/create';
        $actionText = 'Create Your First Cup';
        $padding = 'py-16';
        include __DIR__ . '/../partials/empty_state.php';
        ?>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="table-th">Cup</th>
                        <th class="table-th">Season</th>
                        <th class="table-th">Teams</th>
                        <th class="table-th">Rounds</th>
                        <th class="table-th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cups as $cup): ?>
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="table-td">
                                <a href="<?= $basePath ?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>"
                                    class="font-bold text-text-main no-underline hover:text-primary transition-colors">
                                    <?= htmlspecialchars($cup['name']) ?>
                                </a>
                            </td>
                            <td class="table-td"><span
                                    class="text-text-muted"><?= htmlspecialchars($cup['seasonName'] ?? '') ?></span></td>
                            <td class="table-td"><?= count($cup['teamIds'] ?? []) ?></td>
                            <td class="table-td"><?= count($cup['rounds'] ?? []) ?></td>
                            <td class="table-td text-right">
                                <a href="<?= $basePath ?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>"
                                    class="btn btn-secondary btn-sm mr-2">Bracket</a>
                                <a href="<?= $basePath ?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/fixtures"
                                    class="btn btn-secondary btn-sm">Fixtures</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>