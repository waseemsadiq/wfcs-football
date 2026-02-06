<div class="">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h1 class="text-4xl font-extrabold tracking-tight text-text-main">
            <?= htmlspecialchars($player['name']) ?>
            <?php if (!empty($player['squadNumber'])): ?>
                <span class="text-text-muted font-normal">#<?= htmlspecialchars($player['squadNumber']) ?></span>
            <?php endif; ?>
        </h1>
        <div class="flex gap-4">
            <a href="<?= $basePath ?>/admin/players/<?= htmlspecialchars($player['slug'] ?? $player['id']) ?>/edit"
                class="btn btn-secondary">Edit Player</a>
            <a href="<?= $basePath ?>/admin/players" class="btn btn-secondary">Back to Players</a>
        </div>
    </div>

    <!-- Player Details -->
    <div class="card mb-8">
        <h2 class="text-xl font-bold mb-6 pb-4 border-b border-border">Player Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-[200px_1fr] gap-y-6 gap-x-8">
            <div class="text-text-muted font-medium">Team</div>
            <div>
                <?php if (!empty($player['teamName'])): ?>
                    <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($player['teamSlug'] ?? '') ?>"
                        class="text-primary hover:underline">
                        <?= htmlspecialchars($player['teamName']) ?>
                    </a>
                <?php else: ?>
                    <span class="text-text-muted italic">Pool Player (No team assigned)</span>
                <?php endif; ?>
            </div>

            <div class="text-text-muted font-medium">Position<?= !empty($player['position']) && str_contains($player['position'], ',') ? 's' : '' ?></div>
            <div>
                <?php if (!empty($player['position'])): ?>
                    <?php
                    $positions = explode(',', $player['position']);
                    $positions = array_map('trim', $positions);
                    ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($positions as $pos): ?>
                            <span class="text-xs px-2 py-1 rounded bg-surface-hover">
                                <?= htmlspecialchars($pos) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <span class="text-text-muted italic">Not set</span>
                <?php endif; ?>
            </div>

            <div class="text-text-muted font-medium">Squad Number</div>
            <div>
                <?php if (!empty($player['squadNumber'])): ?>
                    <span class="font-mono font-bold text-lg">#<?= htmlspecialchars($player['squadNumber']) ?></span>
                <?php else: ?>
                    <span class="text-text-muted italic">Not assigned</span>
                <?php endif; ?>
            </div>

            <div class="text-text-muted font-medium">Status</div>
            <div>
                <?php
                $statusColors = [
                    'active' => 'bg-green-500/20 text-green-400',
                    'injured' => 'bg-red-500/20 text-red-400',
                    'suspended' => 'bg-yellow-500/20 text-yellow-400',
                    'unavailable' => 'bg-gray-500/20 text-gray-400',
                ];
                $statusColor = $statusColors[$player['status']] ?? 'bg-gray-500/20 text-gray-400';
                ?>
                <span class="text-sm px-3 py-1 rounded <?= $statusColor ?>">
                    <?= htmlspecialchars(ucfirst($player['status'])) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="card mb-8">
        <h2 class="text-xl font-bold mb-6 pb-4 border-b border-border">Statistics</h2>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-primary mb-2">
                    <?= htmlspecialchars($stats['totalGoals'] ?? 0) ?>
                </div>
                <div class="text-sm text-text-muted">Goals</div>
            </div>

            <div class="text-center">
                <div class="text-3xl font-bold text-primary mb-2">
                    <?= htmlspecialchars($stats['totalAssists'] ?? 0) ?>
                </div>
                <div class="text-sm text-text-muted">Assists</div>
            </div>

            <div class="text-center">
                <div class="text-3xl font-bold text-yellow-400 mb-2">
                    <?= htmlspecialchars($stats['yellowCards'] ?? 0) ?>
                </div>
                <div class="text-sm text-text-muted">Yellow Cards</div>
            </div>

            <div class="text-center">
                <div class="text-3xl font-bold text-red-400 mb-2">
                    <?= htmlspecialchars($stats['redCards'] ?? 0) ?>
                </div>
                <div class="text-sm text-text-muted">Red Cards</div>
            </div>

            <div class="text-center">
                <div class="text-3xl font-bold text-blue-400 mb-2">
                    <?= htmlspecialchars($stats['blueCards'] ?? 0) ?>
                </div>
                <div class="text-sm text-text-muted">Blue Cards</div>
            </div>

            <div class="text-center">
                <div class="text-3xl font-bold text-orange-400 mb-2">
                    <?= htmlspecialchars($stats['sinBins'] ?? 0) ?>
                </div>
                <div class="text-sm text-text-muted">Sin Bins</div>
            </div>

            <div class="text-center">
                <div class="text-3xl font-bold text-primary mb-2">
                    <?= htmlspecialchars($stats['matchesPlayed'] ?? 0) ?>
                </div>
                <div class="text-sm text-text-muted">Matches</div>
            </div>
        </div>
    </div>

    <!-- Recent Match Events -->
    <?php if (!empty($events)): ?>
        <div class="card mb-8">
            <h2 class="text-xl font-bold mb-6 pb-4 border-b border-border">Recent Match Events</h2>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="table-th">Date</th>
                            <th class="table-th">Team</th>
                            <th class="table-th">Event</th>
                            <th class="table-th">Minute</th>
                            <th class="table-th">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr class="hover:bg-surface-hover transition-colors">
                                <td class="table-td">
                                    <?= date('j M Y', strtotime($event['createdAt'])) ?>
                                </td>
                                <td class="table-td">
                                    <?= htmlspecialchars($event['teamName']) ?>
                                </td>
                                <td class="table-td">
                                    <?php
                                    $eventIcons = [
                                        'goal' => '⚽',
                                        'assist' => '🅰️',
                                        'yellow_card' => '🟨',
                                        'red_card' => '🟥',
                                    ];
                                    $icon = $eventIcons[$event['eventType']] ?? '';
                                    ?>
                                    <span><?= $icon ?> <?= htmlspecialchars(str_replace('_', ' ', ucwords($event['eventType'], '_'))) ?></span>
                                </td>
                                <td class="table-td">
                                    <?= !empty($event['minute']) ? htmlspecialchars($event['minute']) . "'" : '-' ?>
                                </td>
                                <td class="table-td">
                                    <?= !empty($event['notes']) ? htmlspecialchars($event['notes']) : '-' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
