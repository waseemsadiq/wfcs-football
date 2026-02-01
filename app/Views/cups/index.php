<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold m-0">Cups</h1>
    <a href="<?=$basePath?>/admin/cups/create" class="btn btn-primary">Create Cup</a>
</div>

<div class="card">
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
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr>
                        <th class="table-th text-left pl-6">Cup</th>
                        <th class="table-th text-left">Season</th>
                        <th class="table-th text-center">Teams</th>
                        <th class="table-th text-center">Rounds</th>
                        <th class="table-th text-right pr-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <?php foreach ($cups as $cup): ?>
                        <tr class="hover:bg-surface-hover/50 transition-colors">
                            <td class="p-4 pl-6">
                                <a href="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>"
                                    class="font-bold text-text-main hover:text-primary transition-colors text-lg">
                                    <?= htmlspecialchars($cup['name']) ?>
                                </a>
                            </td>
                            <td class="p-4 text-text-muted">
                                <?= htmlspecialchars($cup['seasonName'] ?? '') ?>
                            </td>
                            <td class="p-4 text-center text-text-muted">
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-hover text-text-main">
                                    <?= count($cup['teamIds'] ?? []) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center text-text-muted">
                                <span
                                    class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-hover text-text-main">
                                    <?= count($cup['rounds'] ?? []) ?>
                                </span>
                            </td>
                            <td class="p-4 pr-6 text-right space-x-2">
                                <a href="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>"
                                    class="btn btn-sm btn-secondary">Bracket</a>
                                <a href="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/fixtures"
                                    class="btn btn-sm btn-primary">Fixtures</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>