<div class="text-center mb-12">
    <h1
        class="text-4xl font-extrabold tracking-tight mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">
        Leagues</h1>
</div>

<div class="card">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold m-0">All Leagues</h2>
        <a href="<?=$basePath?>/admin/leagues/create" class="btn btn-primary">+ Create League</a>
    </div>

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
                                <a href="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
                                    class="font-bold text-text-main no-underline hover:text-primary transition-colors">
                                    <?= htmlspecialchars($league['name']) ?>
                                </a>
                            </td>
                            <td class="table-td"><span
                                    class="text-text-muted"><?= htmlspecialchars($league['seasonName'] ?? '') ?></span></td>
                            <td class="table-td"><?= count($league['teamIds'] ?? []) ?></td>
                            <td class="table-td"><?= count($league['fixtures'] ?? []) ?></td>
                            <td class="table-td text-right">
                                <a href="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
                                    class="btn btn-secondary btn-sm mr-2">View</a>
                                <a href="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/fixtures"
                                    class="btn btn-secondary btn-sm">Fixtures</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>