<?php
/**
 * Public Top Scorers Page - Leaderboards
 */
?>

<div class="w-full">
    <?php
    $title = 'Top Scorers';
    $subtitle = null;
    include __DIR__ . '/../partials/page_header.php';
    ?>

    <!-- League Filter -->
    <?php if (!empty($leagues)): ?>
        <div class="mb-8 max-w-md mx-auto">
            <div class="card p-6">
                <label for="league-filter" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                    Filter by League
                </label>
                <select id="league-filter"
                    class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                    onchange="filterByLeague(this.value)">
                    <option value="">All Competitions</option>
                    <?php foreach ($leagues as $league): ?>
                        <option value="<?= $league['id'] ?>" <?= ($selectedLeagueId == $league['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($league['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($scorers)): ?>
        <div class="card">
            <div class="text-center py-12 text-text-muted">
                <p>No goals recorded yet.</p>
            </div>
        </div>
    <?php else: ?>
        <!-- Leaderboard -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-surface-hover/50 border-b border-border">
                            <th class="table-th text-center w-20">Rank</th>
                            <th class="table-th text-left">Player</th>
                            <th class="table-th text-left">Team</th>
                            <th class="table-th text-center">Goals</th>
                            <th class="table-th text-center">Assists</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <?php
                        $rank = 1;
                        $prevGoals = null;
                        $displayRank = 1;

                        foreach ($scorers as $index => $scorer):
                            // Handle tied rankings
                            if ($prevGoals !== null && $scorer['totalGoals'] != $prevGoals) {
                                $displayRank = $rank;
                            }

                            // Medal colors for top 3
                            $rankColor = '';
                            if ($displayRank === 1) {
                                $rankColor = 'text-yellow-400';
                            } elseif ($displayRank === 2) {
                                $rankColor = 'text-gray-400';
                            } elseif ($displayRank === 3) {
                                $rankColor = 'text-orange-400';
                            }
                        ?>
                            <tr class="hover:bg-surface-hover/30 transition-colors">
                                <!-- Rank -->
                                <td class="table-td text-center">
                                    <span class="font-bold text-lg <?= $rankColor ?>">
                                        <?= $displayRank ?>
                                    </span>
                                </td>

                                <!-- Player Name -->
                                <td class="table-td">
                                    <a href="<?= $basePath ?>/player/<?= htmlspecialchars($scorer['slug']) ?>"
                                        class="font-semibold text-text-main hover:text-primary transition-colors">
                                        <?= htmlspecialchars($scorer['name']) ?>
                                    </a>
                                </td>

                                <!-- Team -->
                                <td class="table-td">
                                    <?php if ($scorer['team']): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full"
                                                style="background-color: <?= htmlspecialchars($scorer['team']['colour'] ?? '#1a5f2a') ?>"></span>
                                            <a href="<?= $basePath ?>/team/<?= htmlspecialchars($scorer['team']['slug']) ?>"
                                                class="text-text-muted hover:text-primary transition-colors">
                                                <?= htmlspecialchars($scorer['team']['name']) ?>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-text-muted italic">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Goals -->
                                <td class="table-td text-center">
                                    <span class="font-mono font-bold text-primary text-lg">
                                        <?= $scorer['totalGoals'] ?? 0 ?>
                                    </span>
                                </td>

                                <!-- Assists -->
                                <td class="table-td text-center">
                                    <span class="font-mono text-text-muted">
                                        <?= $scorer['totalAssists'] ?? 0 ?>
                                    </span>
                                </td>
                            </tr>
                        <?php
                            $prevGoals = $scorer['totalGoals'];
                            $rank++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Legend for top 3 -->
        <div class="mt-6 flex justify-center gap-6 text-sm text-text-muted">
            <div class="flex items-center gap-2">
                <span class="font-bold text-yellow-400">1st</span>
                <span>Gold</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-400">2nd</span>
                <span>Silver</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold text-orange-400">3rd</span>
                <span>Bronze</span>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function filterByLeague(leagueId) {
        const url = new URL(window.location.href);
        if (leagueId) {
            url.searchParams.set('league_id', leagueId);
        } else {
            url.searchParams.delete('league_id');
        }
        window.location.href = url.toString();
    }
</script>
