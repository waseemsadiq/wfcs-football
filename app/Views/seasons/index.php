<div class="text-center mb-12">
    <h1
        class="text-4xl font-extrabold tracking-tight mb-2 text-text-main">
        Seasons</h1>
</div>

<div class="card">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold m-0">All Seasons</h2>
        <a href="<?=$basePath?>/admin/seasons/create" class="btn btn-primary">+ Create Season</a>
    </div>

    <?php if (empty($seasons)): ?>
        <div class="text-center py-16 px-8">
            <p class="text-text-muted mb-6 text-lg">No seasons created yet.</p>
            <p class="text-text-muted mb-8">Create your first season to start organising your leagues and cups.</p>
            <a href="<?=$basePath?>/admin/seasons/create" class="btn btn-primary">Create Your First Season</a>
        </div>
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
                                <a href="<?=$basePath?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>"
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
                                            action="<?=$basePath?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>/set-active"
                                            class="inline-block">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <button type="submit"
                                                class="btn btn-sm border border-primary text-primary hover:bg-primary/10 bg-transparent">Set
                                                Active</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="<?=$basePath?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>/edit"
                                        class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST"
                                        action="<?=$basePath?>/admin/seasons/<?= htmlspecialchars($season['slug'] ?? $season['id']) ?>/delete"
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