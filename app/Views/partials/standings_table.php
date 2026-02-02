<?php
/**
 * Render a standings table with responsive layout.
 *
 * Variables expected:
 * @var array $standings - Array of standings data
 * @var string $context - 'public' or 'admin' (determines link paths)
 * @var string|null $basePath - Base URL path (optional, for context-aware links)
 */

$context = $context ?? 'public';
$basePath = $basePath ?? '';
?>

<?php if (empty($standings)): ?>
    <div class="text-center py-12 px-4 text-text-muted">
        <p>No standings available</p>
    </div>
<?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th
                        class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12">
                        Pos</th>
                    <th
                        class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-left">
                        Team</th>
                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12"
                        title="Played">P</th>
                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden sm:table-cell"
                        title="Won">W</th>
                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden sm:table-cell"
                        title="Drawn">D</th>
                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden sm:table-cell"
                        title="Lost">L</th>
                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden md:table-cell"
                        title="Goals For">GF</th>
                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden md:table-cell"
                        title="Goals Against">GA</th>
                    <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12"
                        title="Goal Difference">GD</th>
                    <th
                        class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-16">
                        Pts</th>
                    <th
                        class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-left w-32 hidden md:table-cell">
                        Form</th>
                </tr>
            </thead>
            <tbody>
                <?php $pos = 1;
                foreach ($standings as $row): ?>
                    <tr class="border-b border-border hover:bg-surface-hover/50 transition-colors last:border-0">
                        <td class="p-4 text-center font-medium text-text-muted"><?= $pos++ ?></td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <span class="inline-block w-3 h-3 rounded-full shadow-sm flex-shrink-0"
                                    style="background-color: <?= htmlspecialchars($row['teamColour']) ?>"></span>
                                <span class="font-semibold text-text-main">
                                    <?php
                                    $teamUrl = $context === 'admin'
                                        ? $basePath . '/admin/teams/' . htmlspecialchars($row['teamSlug'] ?? $row['teamId'])
                                        : $basePath . '/team/' . htmlspecialchars($row['teamSlug'] ?? $row['teamId']);
                                    ?>
                                    <a href="<?= $teamUrl ?>" class="hover:text-primary transition-colors">
                                        <?= htmlspecialchars($row['teamName']) ?>
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td class="p-4 text-center text-text-muted"><?= $row['played'] ?></td>
                        <td class="p-4 text-center text-text-muted hidden sm:table-cell"><?= $row['won'] ?></td>
                        <td class="p-4 text-center text-text-muted hidden sm:table-cell"><?= $row['drawn'] ?>
                        </td>
                        <td class="p-4 text-center text-text-muted hidden sm:table-cell"><?= $row['lost'] ?>
                        </td>
                        <td class="p-4 text-center text-text-muted hidden md:table-cell"><?= $row['goalsFor'] ?>
                        </td>
                        <td class="p-4 text-center text-text-muted hidden md:table-cell">
                            <?= $row['goalsAgainst'] ?>
                        </td>
                        <td
                            class="p-4 text-center font-medium <?= $row['goalDifference'] > 0 ? 'text-primary' : ($row['goalDifference'] < 0 ? 'text-danger' : 'text-text-muted') ?>">
                            <?= $row['goalDifference'] > 0 ? '+' . $row['goalDifference'] : $row['goalDifference'] ?>
                        </td>
                        <td class="p-4 text-center font-bold text-lg text-white"><?= $row['points'] ?></td>
                        <td class="p-4 hidden md:table-cell">
                            <div class="flex items-center justify-start gap-1">
                                <?php if (!empty($row['form'])): ?>
                                    <?php foreach ($row['form'] as $result): ?>
                                        <?php
                                        $colorClass = match ($result) {
                                            'W' => 'bg-win text-slate-950',
                                            'D' => 'bg-draw text-white',
                                            'L' => 'bg-loss text-white',
                                            default => 'bg-surface-hover text-text-muted'
                                        };
                                        $titleText = match ($result) {
                                            'W' => 'Win',
                                            'D' => 'Draw',
                                            'L' => 'Loss',
                                            default => $result
                                        };
                                        ?>
                                        <span
                                            class="w-5 h-5 flex items-center justify-center rounded text-[10px] font-bold <?= $colorClass ?>"
                                            title="<?= $titleText ?>">
                                            <?= $result ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-text-muted text-xs">-</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>