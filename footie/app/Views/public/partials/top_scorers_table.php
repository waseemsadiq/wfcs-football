<?php
/**
 * Top Scorers Table Partial
 * Used for initial render and AJAX updates
 */
?>

<?php if (empty($scorers)): ?>
    <div class="text-center py-12 text-text-muted">
        <p>No goals recorded yet.</p>
    </div>
<?php else: ?>
    <table class="w-full">
        <thead>
            <tr class="bg-surface-hover/50 border-b border-border">
                <th class="table-th text-center w-20">Rank</th>
                <th class="table-th text-left">Player</th>
                <th class="table-th text-left">Team</th>
                <th class="table-th text-center">Goals</th>
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
                </tr>
            <?php
                $prevGoals = $scorer['totalGoals'];
                $rank++;
            endforeach;
            ?>
        </tbody>
    </table>
<?php endif; ?>
