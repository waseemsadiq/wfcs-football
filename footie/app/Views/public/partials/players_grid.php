<?php if (empty($players)): ?>
    <div class="card">
        <div class="text-center py-12 text-text-muted">
            <p>No players available.</p>
        </div>
    </div>
<?php else: ?>
    <!-- Players Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($players as $player): ?>
            <a href="<?= $basePath ?>/player/<?= htmlspecialchars($player['slug']) ?>"
                class="card hover:border-primary/50 transition-all duration-200 hover:shadow-lg group">
                <div class="p-6">
                    <!-- Player Name -->
                    <h3 class="text-xl font-bold text-text-main mb-2 group-hover:text-primary transition-colors">
                        <?= htmlspecialchars($player['name']) ?>
                    </h3>

                    <!-- Team -->
                    <?php if ($player['team']): ?>
                        <p class="text-sm text-text-muted mb-3 flex items-center gap-2">
                            <span class="inline-block w-3 h-3 rounded-full"
                                style="background-color: <?= htmlspecialchars($player['team']['colour'] ?? '#1a5f2a') ?>"></span>
                            <?= htmlspecialchars($player['team']['name']) ?>
                        </p>
                    <?php endif; ?>

                    <!-- Position and Squad Number -->
                    <div class="flex items-center gap-4 text-sm">
                        <?php if (!empty($player['position'])): ?>
                            <span class="px-3 py-1 bg-surface-hover rounded text-text-muted font-medium">
                                <?= htmlspecialchars($player['position']) ?>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($player['squadNumber'])): ?>
                            <span class="font-mono font-bold text-primary text-lg">
                                #<?= htmlspecialchars($player['squadNumber']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Status Badge -->
                    <?php
                    $statusColors = [
                        'active' => 'bg-green-500/20 text-green-400',
                        'injured' => 'bg-red-500/20 text-red-400',
                        'suspended' => 'bg-yellow-500/20 text-yellow-400',
                        'unavailable' => 'bg-gray-500/20 text-gray-400',
                    ];
                    $statusColor = $statusColors[$player['status']] ?? 'bg-gray-500/20 text-gray-400';
                    ?>
                    <div class="mt-4">
                        <span class="text-xs px-2 py-1 rounded <?= $statusColor ?>">
                            <?= htmlspecialchars(ucfirst($player['status'])) ?>
                        </span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <?php
    // Include pagination
    if (isset($pagination) && $pagination['totalPages'] > 1):
        $containerClass = 'mt-8';
        include __DIR__ . '/../../partials/pagination.php';
    endif;
    ?>
<?php endif; ?>
