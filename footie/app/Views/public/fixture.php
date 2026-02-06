<?php
/**
 * Public Fixture Detail Page
 * Displays comprehensive match information
 */
?>

<div class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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

    <!-- Match Header Card -->
    <div class="card mb-6">
        <div class="p-6 sm:p-8 lg:p-10">
            <!-- Status Badge -->
            <?php if (isset($fixture['status'])): ?>
                <?php
                $statusConfig = [
                    'scheduled' => ['color' => 'bg-blue-500/20 text-blue-400 border-blue-500/30', 'icon' => '📅'],
                    'in_progress' => ['color' => 'bg-green-500/20 text-green-400 border-green-500/30', 'icon' => '🔴'],
                    'completed' => ['color' => 'bg-gray-500/20 text-gray-400 border-gray-500/30', 'icon' => '✓'],
                    'postponed' => ['color' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30', 'icon' => '⏸️'],
                    'cancelled' => ['color' => 'bg-red-500/20 text-red-400 border-red-500/30', 'icon' => '✕'],
                ];
                $config = $statusConfig[$fixture['status']] ?? $statusConfig['scheduled'];
                ?>
                <div class="flex justify-center mb-6">
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full border <?= $config['color'] ?> text-sm font-bold uppercase tracking-wider">
                        <span>
                            <?= $config['icon'] ?>
                        </span>
                        <?= htmlspecialchars(str_replace('_', ' ', $fixture['status'])) ?>
                        <?php if ($fixture['isLive'] ?? false): ?>
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>

            <!-- Teams & Score -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 sm:gap-8 lg:gap-12 mb-8">
                <!-- Home Team -->
                <div class="flex-1 w-full sm:w-auto text-center sm:text-right">
                    <a href="<?= $basePath ?>/team/<?= htmlspecialchars($fixture['homeTeamSlug']) ?>"
                        class="group inline-block">
                        <div class="flex flex-col sm:flex-row items-center sm:justify-end gap-3 sm:gap-4">
                            <div class="order-2 sm:order-1">
                                <div
                                    class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-text-main group-hover:text-primary transition-colors">
                                    <?= htmlspecialchars($fixture['homeTeamName']) ?>
                                </div>
                            </div>
                            <div class="order-1 sm:order-2 w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 rounded-full shadow-lg"
                                style="background-color: <?= htmlspecialchars($fixture['homeTeamColour'] ?? '#1a5f2a') ?>">
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Score -->
                <div class="text-center min-w-[140px] sm:min-w-[160px]">
                    <?php if ($fixture['result']): ?>
                        <div class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-primary leading-none mb-2">
                            <?= $fixture['result']['homeScore'] ?> <span class="text-text-muted">-</span>
                            <?= $fixture['result']['awayScore'] ?>
                        </div>

                        <?php if ($fixtureType === 'cup'): ?>
                            <?php if (!empty($fixture['result']['extraTime'])): ?>
                                <div class="text-sm sm:text-base text-text-muted font-semibold mt-2">
                                    After Extra Time:
                                    <?= $fixture['result']['homeScoreEt'] ?? $fixture['result']['homeScore'] ?> -
                                    <?= $fixture['result']['awayScoreEt'] ?? $fixture['result']['awayScore'] ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($fixture['result']['penalties'])): ?>
                                <div class="text-sm sm:text-base text-text-muted font-semibold mt-1">
                                    Penalties:
                                    <?= $fixture['result']['homePens'] ?? 0 ?> -
                                    <?= $fixture['result']['awayPens'] ?? 0 ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-3xl sm:text-4xl font-bold text-text-muted">vs</div>
                    <?php endif; ?>
                </div>

                <!-- Away Team -->
                <div class="flex-1 w-full sm:w-auto text-center sm:text-left">
                    <a href="<?= $basePath ?>/team/<?= htmlspecialchars($fixture['awayTeamSlug']) ?>"
                        class="group inline-block">
                        <div class="flex flex-col sm:flex-row items-center sm:justify-start gap-3 sm:gap-4">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 rounded-full shadow-lg"
                                style="background-color: <?= htmlspecialchars($fixture['awayTeamColour'] ?? '#1a5f2a') ?>">
                            </div>
                            <div>
                                <div
                                    class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-text-main group-hover:text-primary transition-colors">
                                    <?= htmlspecialchars($fixture['awayTeamName']) ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Match Details -->
            <div
                class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 text-text-muted text-sm sm:text-base border-t border-border pt-6">
                <?php if ($fixture['date']): ?>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium">
                            <?= date('l, j F Y', strtotime($fixture['date'])) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if ($fixture['time']): ?>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">
                            <?= date('g:i A', strtotime($fixture['time'])) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if ($fixture['pitch']): ?>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="font-medium">Pitch
                            <?= htmlspecialchars($fixture['pitch']) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if ($fixture['referee']): ?>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="font-medium">Referee:
                            <?= htmlspecialchars($fixture['referee']) ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Video Section -->
    <div class="card mb-6">
        <div class="p-6 sm:p-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-text-main mb-6 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 text-primary" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Video
            </h2>

            <?php
            $videos = [];
            if ($fixture['status'] === 'in_progress' && $fixture['liveStreamUrl']) {
                $videos['Live Stream'] = $fixture['liveStreamUrl'];
            } else {
                if ($fixture['fullMatchUrl']) {
                    $videos['Full Match Replay'] = $fixture['fullMatchUrl'];
                }
                if ($fixture['highlightsUrl']) {
                    $videos['Match Highlights'] = $fixture['highlightsUrl'];
                }
            }
            ?>

            <?php if (!empty($videos)): ?>
                <?php if (count($videos) > 1): ?>
                    <!-- Tabbed Interface -->
                    <div class="mb-6 mt-2">
                        <div class="gap-2 p-1 bg-surface-hover rounded-lg inline-flex border border-border/50">
                            <?php $i = 0;
                            foreach ($videos as $title => $url): ?>
                                <button onclick="switchVideoTab(<?= $i ?>)" id="video-tab-<?= $i ?>"
                                    class="video-tab-btn px-4 py-2 rounded-md text-sm font-bold uppercase tracking-wider transition-all duration-200 <?= $i === 0 ? 'bg-primary text-primary-text shadow-glow' : 'text-text-muted hover:text-text-main' ?>">
                                    <?= $title ?>
                                </button>
                                <?php $i++; endforeach; ?>
                        </div>
                    </div>

                    <div class="space-y-0">
                        <?php $i = 0;
                        foreach ($videos as $title => $url): ?>
                            <div id="video-container-<?= $i ?>" class="video-content <?= $i === 0 ? '' : 'hidden' ?>">
                                <div class="relative w-full overflow-hidden rounded-lg shadow-2xl bg-surface-hover border border-border/50"
                                    style="padding-bottom: 56.25%; height: 0;">
                                    <iframe src="<?= htmlspecialchars(\Core\View::formatVideoEmbedUrl($url)) ?>"
                                        class="absolute inset-0 w-full h-full" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                            <?php $i++; endforeach; ?>
                    </div>

                    <script>
                        function switchVideoTab(index) {
                            // Hide all video containers and stop hidden videos
                            document.querySelectorAll('.video-content').forEach((el, idx) => {
                                if (idx !== index) {
                                    el.classList.add('hidden');
                                    // Stop iframe playback by resetting src
                                    const iframe = el.querySelector('iframe');
                                    if (iframe) {
                                        const src = iframe.src;
                                        iframe.src = '';
                                        iframe.src = src;
                                    }
                                }
                            });

                            // Show selected container
                            document.getElementById('video-container-' + index).classList.remove('hidden');

                            // Reset all tab buttons
                            document.querySelectorAll('.video-tab-btn').forEach(btn => {
                                btn.classList.remove('bg-primary', 'text-primary-text', 'shadow-glow');
                                btn.classList.add('text-text-muted', 'hover:text-text-main');
                            });

                            // Style active button
                            const activeBtn = document.getElementById('video-tab-' + index);
                            activeBtn.classList.remove('text-text-muted', 'hover:text-text-main');
                            activeBtn.classList.add('bg-primary', 'text-primary-text', 'shadow-glow');
                        }
                    </script>
                <?php else: ?>
                    <!-- Single Video Display -->
                    <?php foreach ($videos as $url): ?>
                        <div class="relative w-full overflow-hidden rounded-lg shadow-2xl bg-surface-hover border border-border/50"
                            style="padding-bottom: 56.25%; height: 0;">
                            <iframe src="<?= htmlspecialchars(\Core\View::formatVideoEmbedUrl($url)) ?>"
                                class="absolute inset-0 w-full h-full" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <?php
                $message = 'No videos available for this match yet';
                $padding = 'py-8';
                include __DIR__ . '/../partials/empty_state.php';
                ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Match Photos Gallery -->
    <div class="card mb-6">
        <div class="p-6 sm:p-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-text-main mb-6 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 text-primary" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Match Gallery
            </h2>

            <?php if (!empty($fixture['photos'])): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($fixture['photos'] as $photo): ?>
                        <div class="group relative overflow-hidden rounded-lg bg-surface-hover shadow-lg hover:shadow-xl transition-shadow"
                            style="aspect-ratio: 16 / 9;">
                            <img src="<?= $basePath ?>/uploads/fixtures/<?= htmlspecialchars($photo['filePath']) ?>"
                                alt="<?= htmlspecialchars($photo['caption'] ?? 'Match photo') ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php if ($photo['caption']): ?>
                                <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/90 to-transparent p-4">
                                    <p class="text-sm text-white font-medium">
                                        <?= htmlspecialchars($photo['caption']) ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <?php
                $message = 'No photos available for this match yet';
                $padding = 'py-8';
                include __DIR__ . '/../partials/empty_state.php';
                ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Match Events Timeline -->
    <?php if (!empty($events)): ?>
        <div class="card mb-6">
            <div class="p-6 sm:p-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-text-main mb-6 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 text-primary" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Match Events
                </h2>
                <div class="space-y-3">
                    <?php if (is_array($events)):
                        foreach ($events as $event): ?>
                            <div
                                class="flex items-start gap-4 p-4 rounded-lg bg-surface-hover/50 hover:bg-surface-hover transition-colors border-l-4 border-primary">
                                <div class="shrink-0 w-14 sm:w-16 text-center">
                                    <span
                                        class="inline-block px-3 py-1 rounded-full bg-primary/20 font-mono font-bold text-primary text-sm sm:text-base">
                                        <?= $event['minute'] ?>'
                                    </span>
                                </div>

                                <!-- Event Icon & Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-1">
                                        <?php if ($event['eventType'] === 'goal'): ?>
                                            <span class="text-3xl">⚽</span>
                                            <div>
                                                <span class="font-bold text-text-main text-lg">
                                                    <?= htmlspecialchars($event['playerName'] ?? 'Unknown') ?>
                                                </span>
                                                <?php if ($event['isOwnGoal'] ?? false): ?>
                                                    <span class="text-red-400 font-semibold ml-2">(Own Goal)</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php elseif ($event['eventType'] === 'yellow_card'): ?>
                                            <div class="w-6 h-8 bg-yellow-400 rounded-sm shadow-md"></div>
                                            <span class="font-bold text-text-main text-lg">
                                                <?= htmlspecialchars($event['playerName'] ?? 'Unknown') ?>
                                            </span>
                                        <?php elseif ($event['eventType'] === 'red_card'): ?>
                                            <div class="w-6 h-8 bg-red-500 rounded-sm shadow-md"></div>
                                            <span class="font-bold text-text-main text-lg">
                                                <?= htmlspecialchars($event['playerName'] ?? 'Unknown') ?>
                                            </span>
                                        <?php elseif ($event['eventType'] === 'blue_card'): ?>
                                            <div class="w-6 h-8 bg-blue-400 rounded-sm shadow-md"></div>
                                            <span class="font-bold text-text-main text-lg">
                                                <?= htmlspecialchars($event['playerName'] ?? 'Unknown') ?>
                                            </span>
                                        <?php elseif ($event['eventType'] === 'sin_bin'): ?>
                                            <span class="text-3xl">⏱️</span>
                                            <span class="font-bold text-text-main text-lg">
                                                <?= htmlspecialchars($event['playerName'] ?? 'Unknown') ?> <span
                                                    class="text-text-muted">(Sin Bin)</span>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($event['teamName']): ?>
                                        <div class="text-sm text-text-muted ml-11">
                                            <?= htmlspecialchars($event['teamName']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Team Squads -->
    <?php if (!empty($homeSquad) || !empty($awaySquad)): ?>
        <div class="card mb-6">
            <div class="p-6 sm:p-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-text-main mb-6 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 text-primary" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Team Squads
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Home Squad -->
                    <div>
                        <h3 class="text-xl font-bold text-text-main mb-4 pb-2 border-b border-border">
                            <?= htmlspecialchars($fixture['homeTeamName']) ?>
                        </h3>
                        <?php if (!empty($homeSquad)): ?>
                            <div class="space-y-2">
                                <?php
                                // Create map of player IDs who have events in this match
                                $homePlayerEvents = [];
                                if (is_array($events)) {
                                    foreach ($events as $event) {
                                        if ($event['teamId'] == $fixture['homeTeamId']) {
                                            $playerId = $event['playerId'];
                                            if (!isset($homePlayerEvents[$playerId])) {
                                                $homePlayerEvents[$playerId] = [];
                                            }
                                            $homePlayerEvents[$playerId][] = $event['eventType'];
                                        }
                                    }
                                }
                                ?>
                                <?php foreach ($homeSquad as $player): ?>
                                    <div
                                        class="flex items-center gap-3 p-3 rounded-lg bg-surface-hover/30 hover:bg-surface-hover/50 transition-colors <?= isset($homePlayerEvents[$player['id']]) ? 'border-l-4 border-primary' : '' ?>">
                                        <div class="shrink-0 w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                                            <span class="font-bold text-primary text-sm">
                                                <?= $player['squadNumber'] ? htmlspecialchars($player['squadNumber']) : '—' ?>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <a href="<?= $basePath ?>/player/<?= htmlspecialchars($player['slug']) ?>"
                                                class="font-semibold text-text-main hover:text-primary transition-colors">
                                                <?= htmlspecialchars($player['name']) ?>
                                            </a>
                                            <div class="text-xs text-text-muted">
                                                <?= htmlspecialchars($player['position'] ?? 'Unknown') ?>
                                            </div>
                                        </div>
                                        <?php if (isset($homePlayerEvents[$player['id']])): ?>
                                            <div class="flex gap-1 shrink-0">
                                                <?php foreach ($homePlayerEvents[$player['id']] as $eventType): ?>
                                                    <?php if ($eventType === 'goal'): ?>
                                                        <span class="text-lg">⚽</span>
                                                    <?php elseif ($eventType === 'yellow_card'): ?>
                                                        <div class="w-3 h-4 bg-yellow-400 rounded-sm"></div>
                                                    <?php elseif ($eventType === 'red_card'): ?>
                                                        <div class="w-3 h-4 bg-red-500 rounded-sm"></div>
                                                    <?php elseif ($eventType === 'blue_card'): ?>
                                                        <div class="w-3 h-4 bg-blue-400 rounded-sm"></div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <?php
                            $message = 'Squad information not available';
                            $padding = 'py-8';
                            include __DIR__ . '/../partials/empty_state.php';
                            ?>
                        <?php endif; ?>
                    </div>

                    <!-- Away Squad -->
                    <div>
                        <h3 class="text-xl font-bold text-text-main mb-4 pb-2 border-b border-border">
                            <?= htmlspecialchars($fixture['awayTeamName']) ?>
                        </h3>
                        <?php if (!empty($awaySquad)): ?>
                            <div class="space-y-2">
                                <?php
                                // Create map of player IDs who have events in this match
                                $awayPlayerEvents = [];
                                if (is_array($events)) {
                                    foreach ($events as $event) {
                                        if ($event['teamId'] == $fixture['awayTeamId']) {
                                            $playerId = $event['playerId'];
                                            if (!isset($awayPlayerEvents[$playerId])) {
                                                $awayPlayerEvents[$playerId] = [];
                                            }
                                            $awayPlayerEvents[$playerId][] = $event['eventType'];
                                        }
                                    }
                                }
                                ?>
                                <?php foreach ($awaySquad as $player): ?>
                                    <div
                                        class="flex items-center gap-3 p-3 rounded-lg bg-surface-hover/30 hover:bg-surface-hover/50 transition-colors <?= isset($awayPlayerEvents[$player['id']]) ? 'border-l-4 border-primary' : '' ?>">
                                        <div class="shrink-0 w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                                            <span class="font-bold text-primary text-sm">
                                                <?= $player['squadNumber'] ? htmlspecialchars($player['squadNumber']) : '—' ?>
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <a href="<?= $basePath ?>/player/<?= htmlspecialchars($player['slug']) ?>"
                                                class="font-semibold text-text-main hover:text-primary transition-colors">
                                                <?= htmlspecialchars($player['name']) ?>
                                            </a>
                                            <div class="text-xs text-text-muted">
                                                <?= htmlspecialchars($player['position'] ?? 'Unknown') ?>
                                            </div>
                                        </div>
                                        <?php if (isset($awayPlayerEvents[$player['id']])): ?>
                                            <div class="flex gap-1 shrink-0">
                                                <?php foreach ($awayPlayerEvents[$player['id']] as $eventType): ?>
                                                    <?php if ($eventType === 'goal'): ?>
                                                        <span class="text-lg">⚽</span>
                                                    <?php elseif ($eventType === 'yellow_card'): ?>
                                                        <div class="w-3 h-4 bg-yellow-400 rounded-sm"></div>
                                                    <?php elseif ($eventType === 'red_card'): ?>
                                                        <div class="w-3 h-4 bg-red-500 rounded-sm"></div>
                                                    <?php elseif ($eventType === 'blue_card'): ?>
                                                        <div class="w-3 h-4 bg-blue-400 rounded-sm"></div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <?php
                            $message = 'Squad information not available';
                            $padding = 'py-8';
                            include __DIR__ . '/../partials/empty_state.php';
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Match Report -->
    <?php if (!empty($fixture['matchReport'])): ?>
        <div class="card mb-6">
            <div class="p-6 sm:p-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-text-main mb-6 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 text-primary" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Match Report
                </h2>
                <div class="prose prose-invert prose-lg max-w-none text-text-main leading-relaxed">
                    <?= nl2br(htmlspecialchars($fixture['matchReport'])) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>