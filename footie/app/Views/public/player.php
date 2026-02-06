<?php
/**
 * Public Player Profile Page
 */
?>

<div class="w-full">
    <!-- Player Header -->
    <div class="mb-12">
        <div class="card">
            <div class="p-8">
                <!-- Back Link -->
                <a href="<?= $basePath ?>/players"
                    class="inline-flex items-center gap-2 text-text-muted hover:text-primary transition-colors mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Players
                </a>

                <!-- Player Name -->
                <h1 class="text-4xl font-extrabold text-text-main mb-4">
                    <?= htmlspecialchars($player['name']) ?>
                </h1>

                <!-- Player Details -->
                <div class="flex flex-wrap items-center gap-6 text-text-muted">
                    <?php if ($player['team']): ?>
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-4 h-4 rounded-full"
                                style="background-color: <?= htmlspecialchars($player['team']['colour'] ?? '#1a5f2a') ?>"></span>
                            <a href="<?= $basePath ?>/team/<?= htmlspecialchars($player['team']['slug']) ?>"
                                class="font-semibold hover:text-primary transition-colors">
                                <?= htmlspecialchars($player['team']['name']) ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($player['position'])): ?>
                        <div class="font-semibold">
                            <?= htmlspecialchars($player['position']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($player['squadNumber'])): ?>
                        <div class="font-mono font-bold text-primary text-2xl">
                            #<?= htmlspecialchars($player['squadNumber']) ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    $statusColors = [
                        'active' => 'bg-green-500/20 text-green-400',
                        'injured' => 'bg-red-500/20 text-red-400',
                        'suspended' => 'bg-yellow-500/20 text-yellow-400',
                        'unavailable' => 'bg-gray-500/20 text-gray-400',
                    ];
                    $statusColor = $statusColors[$player['status']] ?? 'bg-gray-500/20 text-gray-400';
                    ?>
                    <span class="px-3 py-1 rounded <?= $statusColor ?> text-sm font-semibold">
                        <?= htmlspecialchars(ucfirst($player['status'])) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Player Statistics -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-text-main mb-6">Season Statistics</h2>
        <div class="grid grid-cols-3 gap-4">
            <!-- Goals -->
            <div class="card text-center">
                <div class="p-6">
                    <div class="text-4xl font-extrabold text-primary mb-2">
                        <?= $stats['totalGoals'] ?? 0 ?>
                    </div>
                    <div class="text-sm font-semibold text-text-muted uppercase tracking-wide">
                        Goals
                    </div>
                </div>
            </div>

            <!-- Assists -->
            <div class="card text-center">
                <div class="p-6">
                    <div class="text-4xl font-extrabold text-blue-500 mb-2">
                        <?= $stats['totalAssists'] ?? 0 ?>
                    </div>
                    <div class="text-sm font-semibold text-text-muted uppercase tracking-wide">
                        Assists
                    </div>
                </div>
            </div>

            <!-- Sin Bins -->
            <div class="card text-center">
                <div class="p-6">
                    <div class="text-4xl font-extrabold text-purple-500 mb-2">
                        <?= $stats['sinBins'] ?? 0 ?>
                    </div>
                    <div class="text-sm font-semibold text-text-muted uppercase tracking-wide">
                        Sin Bins
                    </div>
                </div>
            </div>

            <!-- Blue Cards -->
            <div class="card text-center">
                <div class="p-6">
                    <div class="text-4xl font-extrabold text-blue-400 mb-2">
                        <?= $stats['blueCards'] ?? 0 ?>
                    </div>
                    <div class="text-sm font-semibold text-text-muted uppercase tracking-wide">
                        Blue Cards
                    </div>
                </div>
            </div>

            <!-- Yellow Cards -->
            <div class="card text-center">
                <div class="p-6">
                    <div class="text-4xl font-extrabold text-yellow-500 mb-2">
                        <?= $stats['yellowCards'] ?? 0 ?>
                    </div>
                    <div class="text-sm font-semibold text-text-muted uppercase tracking-wide">
                        Yellow Cards
                    </div>
                </div>
            </div>

            <!-- Red Cards -->
            <div class="card text-center">
                <div class="p-6">
                    <div class="text-4xl font-extrabold text-red-500 mb-2">
                        <?= $stats['redCards'] ?? 0 ?>
                    </div>
                    <div class="text-sm font-semibold text-text-muted uppercase tracking-wide">
                        Red Cards
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events -->
    <?php if (!empty($events)): ?>
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-text-main mb-6">Recent Events</h2>
            <div class="card">
                <div class="divide-y divide-border">
                    <?php foreach ($events as $event): ?>
                        <div class="p-6 hover:bg-surface-hover/30 transition-colors">
                            <div class="flex items-start gap-4">
                                <!-- Event Icon -->
                                <div class="shrink-0 mt-1">
                                    <?php if ($event['eventType'] === 'goal'): ?>
                                        <div
                                            class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 3.3l2.8 2.04-1.07 3.29H10.3l-1.07-3.3L12 5.3zM7.54 8.59l2.8-2.04.53 1.62-1.84 1.34-1.5-1.03zm8.92 0l-1.5 1.03-1.84-1.34.53-1.62 2.8 2.04zM12 17.7l-2.8-2.04 1.07-3.29h3.46l1.07 3.29L12 17.7zm-4.46-.89l1.5-1.03 1.84 1.34-.53 1.62-2.8-2.04zm8.92 0l-2.8 2.04-.53-1.62 1.84-1.34 1.5 1.03z"/></svg>
                                        </div>
                                    <?php elseif ($event['eventType'] === 'yellow_card'): ?>
                                        <div class="w-10 h-10 rounded-full bg-yellow-500/20 flex items-center justify-center">
                                            <div class="w-5 h-7 bg-yellow-400 rounded-sm"></div>
                                        </div>
                                    <?php elseif ($event['eventType'] === 'red_card'): ?>
                                        <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                                            <div class="w-5 h-7 bg-red-500 rounded-sm"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Event Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-text-main mb-1">
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $event['eventType']))) ?>
                                        <?php if ($event['isOwnGoal'] ?? false): ?>
                                            <span class="text-red-400">(Own Goal)</span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($event['competition'] && $event['fixture']): ?>
                                        <div class="text-sm text-text-muted mb-2">
                                            <?= htmlspecialchars($event['competition']['name']) ?>
                                        </div>
                                        <div class="text-sm text-text-muted">
                                            <?= htmlspecialchars($event['homeTeam']['name'] ?? '') ?>
                                            vs
                                            <?= htmlspecialchars($event['awayTeam']['name'] ?? '') ?>
                                            <?php if (!empty($event['fixture']['date'])): ?>
                                                • <?= date('j M Y', strtotime($event['fixture']['date'])) ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="p-8 text-center text-text-muted">
                <p>No recorded events for this player yet.</p>
            </div>
        </div>
    <?php endif; ?>
</div>