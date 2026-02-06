<?php
/**
 * Public Fixture Detail Page
 * Displays comprehensive match information including events, report, and media
 */
?>

<div class="w-full">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-text-muted">
            <a href="<?= $basePath ?>/<?= $fixtureType ?>s" class="hover:text-primary transition-colors">
                <?= ucfirst($fixtureType) ?>s
            </a>
            <span>›</span>
            <a href="<?= $basePath ?>/<?= $fixtureType ?>/<?= $competition['slug'] ?>"
               class="hover:text-primary transition-colors">
                <?= htmlspecialchars($competition['name']) ?>
            </a>
            <span>›</span>
            <span class="text-text-main">Match Details</span>
        </div>
    </div>

    <!-- Match Header -->
    <div class="card mb-8">
        <div class="p-8">
            <!-- Teams & Score -->
            <div class="flex items-center justify-center gap-8 mb-6">
                <!-- Home Team -->
                <div class="flex-1 text-right">
                    <a href="<?= $basePath ?>/team/<?= htmlspecialchars($fixture['homeTeamSlug']) ?>"
                       class="group">
                        <div class="inline-flex items-center gap-3">
                            <span class="text-3xl font-extrabold text-text-main group-hover:text-primary transition-colors">
                                <?= htmlspecialchars($fixture['homeTeamName']) ?>
                            </span>
                            <div class="w-12 h-12 rounded-full"
                                 style="background-color: <?= htmlspecialchars($fixture['homeTeamColour'] ?? '#1a5f2a') ?>">
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Score -->
                <div class="text-center min-w-[120px]">
                    <?php if ($fixture['result']): ?>
                        <div class="text-5xl font-extrabold text-primary mb-2">
                            <?= $fixture['result']['homeScore'] ?> - <?= $fixture['result']['awayScore'] ?>
                        </div>
                        <?php if ($fixtureType === 'cup' && $fixture['result']['extraTime']): ?>
                            <div class="text-sm text-text-muted">
                                AET: <?= $fixture['result']['homeScoreEt'] ?> - <?= $fixture['result']['awayScoreEt'] ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($fixtureType === 'cup' && $fixture['result']['penalties']): ?>
                            <div class="text-sm text-text-muted">
                                Pens: <?= $fixture['result']['homePens'] ?> - <?= $fixture['result']['awayPens'] ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-2xl font-bold text-text-muted">vs</div>
                    <?php endif; ?>
                </div>

                <!-- Away Team -->
                <div class="flex-1 text-left">
                    <a href="<?= $basePath ?>/team/<?= htmlspecialchars($fixture['awayTeamSlug']) ?>"
                       class="group">
                        <div class="inline-flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full"
                                 style="background-color: <?= htmlspecialchars($fixture['awayTeamColour'] ?? '#1a5f2a') ?>">
                            </div>
                            <span class="text-3xl font-extrabold text-text-main group-hover:text-primary transition-colors">
                                <?= htmlspecialchars($fixture['awayTeamName']) ?>
                            </span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Match Info -->
            <div class="flex items-center justify-center gap-6 text-text-muted text-sm">
                <?php if ($fixture['date']): ?>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span><?= date('l, j F Y', strtotime($fixture['date'])) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($fixture['time']): ?>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><?= date('g:i A', strtotime($fixture['time'])) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($fixture['pitch']): ?>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Pitch <?= htmlspecialchars($fixture['pitch']) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($fixture['referee']): ?>
                    <div class="flex items-center gap-2">
                        <span>Referee: <?= htmlspecialchars($fixture['referee']) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Status Badge -->
            <?php if (isset($fixture['status'])): ?>
                <?php
                $statusColors = [
                    'scheduled' => 'bg-blue-500/20 text-blue-400',
                    'in_progress' => 'bg-green-500/20 text-green-400',
                    'completed' => 'bg-gray-500/20 text-gray-400',
                    'postponed' => 'bg-yellow-500/20 text-yellow-400',
                    'cancelled' => 'bg-red-500/20 text-red-400',
                ];
                $statusColor = $statusColors[$fixture['status']] ?? 'bg-gray-500/20 text-gray-400';
                ?>
                <div class="mt-4 text-center">
                    <span class="inline-block px-4 py-2 rounded <?= $statusColor ?> text-sm font-semibold uppercase tracking-wide">
                        <?= htmlspecialchars(str_replace('_', ' ', $fixture['status'])) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Match Events Timeline -->
    <?php if (!empty($events)): ?>
        <div class="card mb-8">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-text-main mb-6">Match Events</h2>
                <div class="space-y-4">
                    <?php foreach ($events as $event): ?>
                        <div class="flex items-start gap-4 py-3 border-l-4 border-primary pl-4">
                            <!-- Minute -->
                            <div class="flex-shrink-0 w-12 text-center">
                                <span class="font-mono font-bold text-primary"><?= $event['minute'] ?>'</span>
                            </div>

                            <!-- Event Icon & Details -->
                            <div class="flex-1">
                                <?php if ($event['eventType'] === 'goal'): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl">⚽</span>
                                        <span class="font-semibold text-text-main">
                                            <?= htmlspecialchars($event['playerName'] ?? 'Unknown') ?>
                                            <?php if ($event['isOwnGoal'] ?? false): ?>
                                                <span class="text-red-400">(Own Goal)</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                <?php elseif ($event['eventType'] === 'yellow_card'): ?>
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-7 bg-yellow-400 rounded-sm"></div>
                                        <span class="font-semibold text-text-main">
                                            <?= htmlspecialchars($event['playerName'] ?? 'Unknown') ?>
                                        </span>
                                    </div>
                                <?php elseif ($event['eventType'] === 'red_card'): ?>
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-7 bg-red-500 rounded-sm"></div>
                                        <span class="font-semibold text-text-main">
                                            <?= htmlspecialchars($event['playerName'] ?? 'Unknown') ?>
                                        </span>
                                    </div>
                                <?php elseif ($event['eventType'] === 'blue_card'): ?>
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-7 bg-blue-400 rounded-sm"></div>
                                        <span class="font-semibold text-text-main">
                                            <?= htmlspecialchars($event['playerName'] ?? 'Unknown') ?>
                                        </span>
                                    </div>
                                <?php elseif ($event['eventType'] === 'sin_bin'): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl">⏱️</span>
                                        <span class="font-semibold text-text-main">
                                            <?= htmlspecialchars($event['playerName'] ?? 'Unknown') ?> (Sin Bin)
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($event['teamName']): ?>
                                    <div class="text-sm text-text-muted mt-1">
                                        <?= htmlspecialchars($event['teamName']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Match Report -->
    <?php if (!empty($fixture['matchReport'])): ?>
        <div class="card mb-8">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-text-main mb-6">Match Report</h2>
                <div class="prose prose-invert max-w-none">
                    <?= nl2br(htmlspecialchars($fixture['matchReport'])) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Match Photos -->
    <?php if (!empty($fixture['photos'])): ?>
        <div class="card mb-8">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-text-main mb-6">Match Photos</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($fixture['photos'] as $photo): ?>
                        <div class="group relative aspect-video overflow-hidden rounded-lg">
                            <img src="<?= $basePath ?>/uploads/fixtures/<?= htmlspecialchars($photo['filePath']) ?>"
                                 alt="<?= htmlspecialchars($photo['caption'] ?? 'Match photo') ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            <?php if ($photo['caption']): ?>
                                <div class="absolute bottom-0 left-0 right-0 bg-black/75 p-2">
                                    <p class="text-sm text-white"><?= htmlspecialchars($photo['caption']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Video Section -->
    <?php if ($fixture['liveStreamUrl'] || $fixture['fullMatchUrl'] || $fixture['highlightsUrl']): ?>
        <div class="card mb-8">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-text-main mb-6">Video</h2>

                <?php if ($fixture['status'] === 'in_progress' && $fixture['liveStreamUrl']): ?>
                    <!-- Live Stream -->
                    <div class="aspect-video bg-black rounded-lg overflow-hidden mb-4">
                        <iframe src="<?= htmlspecialchars($fixture['liveStreamUrl']) ?>"
                                class="w-full h-full"
                                frameborder="0"
                                allowfullscreen></iframe>
                    </div>
                    <p class="text-center text-sm text-text-muted">🔴 Live Stream</p>

                <?php elseif ($fixture['fullMatchUrl']): ?>
                    <!-- Full Match -->
                    <div class="aspect-video bg-black rounded-lg overflow-hidden mb-4">
                        <iframe src="<?= htmlspecialchars($fixture['fullMatchUrl']) ?>"
                                class="w-full h-full"
                                frameborder="0"
                                allowfullscreen></iframe>
                    </div>
                    <p class="text-center text-sm text-text-muted">Full Match Replay</p>

                <?php elseif ($fixture['highlightsUrl']): ?>
                    <!-- Highlights -->
                    <div class="aspect-video bg-black rounded-lg overflow-hidden mb-4">
                        <iframe src="<?= htmlspecialchars($fixture['highlightsUrl']) ?>"
                                class="w-full h-full"
                                frameborder="0"
                                allowfullscreen></iframe>
                    </div>
                    <p class="text-center text-sm text-text-muted">Match Highlights</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Back Link -->
    <div class="text-center">
        <a href="<?= $basePath ?>/<?= $fixtureType ?>/<?= $competition['slug'] ?>"
           class="inline-flex items-center gap-2 text-text-muted hover:text-primary transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to <?= htmlspecialchars($competition['name']) ?>
        </a>
    </div>
</div>
