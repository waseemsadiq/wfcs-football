<?php
// Sort fixtures by date desc, then time
usort($fixtures, function ($a, $b) {
    if ($a['date'] === $b['date']) {
        return strcmp($a['time'], $b['time']);
    }
    return strcmp($a['date'], $b['date']);
});
?>

<div class="text-center mb-12">
    <h1 class="text-4xl font-extrabold tracking-tight mb-2 text-text-main">
        <?= htmlspecialchars($league['name']) ?>
    </h1>
    <h2 class="text-2xl font-bold mb-6 text-text-muted">Fixtures</h2>
</div>

<div class="card">
    <div class="flex justify-end gap-4 mb-8">
        <a href="<?= $basePath ?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
            class="btn btn-secondary">
            Back to League
        </a>
    </div>

    <?php
    $modalId = 'regenerateModal';
    $competitionType = 'league';
    $formAction = $basePath . '/admin/leagues/' . htmlspecialchars($league['slug'] ?? $league['id']) . '/regenerate-fixtures';
    $competition = $league;
    include __DIR__ . '/../partials/regenerate_modal.php';
    ?>

    <?php if (empty($fixtures)): ?>
        <div class="text-center p-12">
            <p class="text-text-muted">No fixtures scheduled yet.</p>
        </div>
    <?php else: ?>
        <?php
        $groupedFixtures = [];
        foreach ($fixtures as $fixture) {
            $date = $fixture['date'];
            if (!isset($groupedFixtures[$date])) {
                $groupedFixtures[$date] = [];
            }
            $groupedFixtures[$date][] = $fixture;
        }
        ?>

        <?php foreach ($groupedFixtures as $date => $dateFixtures): ?>
            <div class="mb-12">
                <h3 class="bg-surface-hover py-3 px-6 -mx-8 mb-6 border-y border-border text-lg text-primary font-bold">
                    <?= date('l, j F Y', strtotime($date)) ?>
                </h3>

                <div class="grid gap-4">
                    <?php foreach ($dateFixtures as $fixture): ?>
                        <div class="bg-background border border-border rounded-sm p-6">
                            <form method="POST"
                                action="<?= $basePath ?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/fixtures">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="fixtureId" value="<?= htmlspecialchars($fixture['id']) ?>">

                                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-8 mb-4">
                                    <!-- Home Team -->
                                    <div class="text-right flex items-center justify-end gap-4">
                                        <strong class="text-lg"><?= htmlspecialchars($fixture['homeTeamName']) ?></strong>
                                        <span class="inline-block w-4 h-4 rounded-full bg-current"
                                            style="color: <?= htmlspecialchars($fixture['homeTeamColour'] ?? '#000') ?>;"></span>
                                    </div>

                                    <!-- Scores -->
                                    <div class="flex items-center gap-4 bg-surface px-4 py-2 rounded-sm border border-border">
                                        <input type="number" name="homeScore" min="0" max="99"
                                            class="w-12 text-center p-1 text-xl font-bold text-primary bg-transparent border-none focus:ring-0 appearance-none"
                                            value="<?= $fixture['result'] !== null ? htmlspecialchars((string) $fixture['result']['homeScore']) : '' ?>"
                                            placeholder="-">
                                        <span class="text-text-muted font-bold">vs</span>
                                        <input type="number" name="awayScore" min="0" max="99"
                                            class="w-12 text-center p-1 text-xl font-bold text-primary bg-transparent border-none focus:ring-0 appearance-none"
                                            value="<?= $fixture['result'] !== null ? htmlspecialchars((string) $fixture['result']['awayScore']) : '' ?>"
                                            placeholder="-">
                                    </div>

                                    <!-- Away Team -->
                                    <div class="text-left flex items-center justify-start gap-4">
                                        <span class="inline-block w-4 h-4 rounded-full bg-current"
                                            style="color: <?= htmlspecialchars($fixture['awayTeamColour'] ?? '#000') ?>;"></span>
                                        <strong class="text-lg"><?= htmlspecialchars($fixture['awayTeamName']) ?></strong>
                                    </div>
                                </div>

                                <div class="flex justify-between items-start border-t border-border pt-4 mt-4">
                                    <div class="flex-1">
                                        <details class="group"
                                            data-home-team-id="<?= $fixture['homeTeamId'] ?>"
                                            data-away-team-id="<?= $fixture['awayTeamId'] ?>">
                                            <summary
                                                class="cursor-pointer text-text-muted text-sm hover:text-primary transition-colors flex items-center gap-1 py-1 select-none w-max">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 transition-transform group-open:rotate-90" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                                <span>Match Details (Scorers & Cards)</span>
                                            </summary>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-4 bg-surface p-6 rounded-sm">
                                                <?php foreach (['home', 'away'] as $side): ?>
                                                    <div>
                                                        <label
                                                            class="block mb-4 text-xs uppercase tracking-wide font-semibold text-text-muted border-b border-border pb-2">
                                                            <?= ucfirst($side) ?> Details
                                                        </label>

                                                        <!-- Scorers -->
                                                        <div class="mb-4">
                                                            <div class="flex justify-between items-center mb-1">
                                                                <span class="text-xs font-bold">Scorers</span>
                                                                <button type="button" class="text-xs text-primary hover:underline"
                                                                    onclick="addScorerRow(this, '<?= $side ?>Scorers')">+ Add</button>
                                                            </div>
                                                            <div class="scorers-list space-y-1">
                                                                <?php
                                                                $scorers = $fixture['result'][$side . 'Scorers'] ?? [];
                                                                if (!is_array($scorers))
                                                                    $scorers = [];
                                                                foreach ($scorers as $index => $scorer):
                                                                    $teamId = $side === 'home' ? $fixture['homeTeamId'] : $fixture['awayTeamId'];
                                                                    $players = $teamPlayers[$teamId] ?? [];
                                                                ?>
                                                                    <div class="flex gap-1 items-center">
                                                                        <select name="<?= $side ?>Scorers[<?= $index ?>][player]"
                                                                            class="form-input py-1 px-2 text-xs flex-1">
                                                                            <option value="">Select player...</option>
                                                                            <?php foreach ($players as $player): ?>
                                                                                <option value="<?= htmlspecialchars($player['name']) ?>"
                                                                                    <?= ($scorer['player'] ?? '') === $player['name'] ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($player['name']) ?>
                                                                                    <?php if (!empty($player['squadNumber'])): ?>
                                                                                        (#<?= $player['squadNumber'] ?>)
                                                                                    <?php endif; ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                        <input type="text" name="<?= $side ?>Scorers[<?= $index ?>][minute]"
                                                                            value="<?= htmlspecialchars((string) ($scorer['minute'] ?? '')) ?>"
                                                                            class="form-input py-1 px-1 text-xs w-10 text-center"
                                                                            placeholder="Min">
                                                                        <label
                                                                            class="flex items-center gap-1 text-[10px] text-text-muted cursor-pointer whitespace-nowrap">
                                                                            <input type="checkbox"
                                                                                name="<?= $side ?>Scorers[<?= $index ?>][ownGoal]" value="1"
                                                                                <?= ($scorer['ownGoal'] ?? false) ? 'checked' : '' ?>> OG
                                                                        </label>
                                                                        <button type="button"
                                                                            class="text-red-500 hover:text-red-400 text-xs px-1"
                                                                            onclick="this.parentElement.remove()">×</button>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>

                                                        <div class="space-y-2">
                                                            <div class="flex justify-between items-center mb-1">
                                                                <span class="text-xs font-bold">Cards</span>
                                                                <button type="button" class="text-xs text-primary hover:underline"
                                                                    onclick="addCardRow(this, '<?= $side ?>CardsCombined')">+
                                                                    Add</button>
                                                            </div>
                                                            <div class="cards-list space-y-1">
                                                                <?php
                                                                $cards = $fixture['result'][$side . 'Cards'] ?? [];
                                                                if (!is_array($cards))
                                                                    $cards = [];
                                                                $cardTypes = [
                                                                    'sinBins' => 'Sin Bin',
                                                                    'blue' => 'Blue',
                                                                    'yellow' => 'Yellow',
                                                                    'red' => 'Red'
                                                                ];
                                                                $allCards = [];
                                                                foreach ($cards as $typeKey => $typeCards) {
                                                                    if (!is_array($typeCards))
                                                                        continue;
                                                                    foreach ($typeCards as $card) {
                                                                        $card['type'] = $typeKey;
                                                                        $allCards[] = $card;
                                                                    }
                                                                }
                                                                foreach ($allCards as $idx => $card):
                                                                    $teamId = $side === 'home' ? $fixture['homeTeamId'] : $fixture['awayTeamId'];
                                                                    $players = $teamPlayers[$teamId] ?? [];
                                                                ?>
                                                                    <div class="flex gap-1 items-center">
                                                                        <select name="<?= $side ?>CardsCombined[<?= $idx ?>][type]"
                                                                            class="form-input py-1 px-1 text-[10px] w-20">
                                                                            <?php foreach ($cardTypes as $val => $lbl): ?>
                                                                                <option value="<?= $val ?>" <?= ($card['type'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                        <select name="<?= $side ?>CardsCombined[<?= $idx ?>][player]"
                                                                            class="form-input py-1 px-2 text-xs flex-1">
                                                                            <option value="">Select player...</option>
                                                                            <?php foreach ($players as $player): ?>
                                                                                <option value="<?= htmlspecialchars($player['name']) ?>"
                                                                                    <?= ($card['player'] ?? '') === $player['name'] ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($player['name']) ?>
                                                                                    <?php if (!empty($player['squadNumber'])): ?>
                                                                                        (#<?= $player['squadNumber'] ?>)
                                                                                    <?php endif; ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                        <input type="text"
                                                                            name="<?= $side ?>CardsCombined[<?= $idx ?>][minute]"
                                                                            value="<?= htmlspecialchars($card['minute'] ?? '') ?>"
                                                                            class="form-input py-1 px-1 text-xs w-10 text-center"
                                                                            placeholder="Min">
                                                                        <button type="button"
                                                                            class="text-red-500 hover:text-red-400 text-xs px-1"
                                                                            onclick="this.parentElement.remove()">×</button>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>

                                                <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 border-t border-border pt-6 mt-4">
                                                    <!-- Row 1: Date | Referee -->
                                                <div class="flex flex-col">
                                                    <label class="text-xs text-text-muted uppercase mb-1">Date</label>
                                                    <input type="date" name="date" value="<?= htmlspecialchars($fixture['date']) ?>"
                                                        class="bg-transparent border border-border text-text-main p-1 rounded focus:border-primary focus:outline-none text-xs">
                                                </div>
                                                <div class="flex flex-col">
                                                    <label class="text-xs text-text-muted uppercase mb-1">Referee</label>
                                                    <select name="referee_id"
                                                        class="bg-transparent border border-border text-text-main p-1 rounded focus:border-primary focus:outline-none text-xs">
                                                        <option value="">No referee assigned</option>
                                                        <?php foreach ($referees as $referee): ?>
                                                            <option value="<?= htmlspecialchars($referee['id']) ?>"
                                                                <?= ($fixture['refereeId'] ?? null) == $referee['id'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($referee['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- Row 2: Time | Pitch -->
                                                <div class="flex flex-col">
                                                    <label class="text-xs text-text-muted uppercase mb-1">Time</label>
                                                    <input type="time" name="time"
                                                        value="<?= htmlspecialchars(substr($fixture['time'], 0, 5)) ?>"
                                                        class="bg-transparent border border-border text-text-main p-1 rounded focus:border-primary focus:outline-none text-xs">
                                                </div>
                                                <div class="flex flex-col">
                                                    <label class="text-xs text-text-muted uppercase mb-1">Pitch</label>
                                                    <input type="text" name="pitch"
                                                        value="<?= htmlspecialchars($fixture['pitch'] ?? '') ?>"
                                                        class="bg-transparent border border-border text-text-main p-1 rounded focus:border-primary focus:outline-none text-xs"
                                                        placeholder="Pitch">
                                                </div>

                                                <!-- Row 3: Week | Live -->
                                                <div class="flex items-center justify-between bg-surface-hover/20 p-2 rounded">
                                                    <span class="text-xs text-text-muted uppercase font-bold">Match Week</span>
                                                    <?php
                                                    // Simple auto-calculation based on unique dates
                                                    static $dates = [];
                                                    if (!in_array($fixture['date'], $dates)) {
                                                        $dates[] = $fixture['date'];
                                                    }
                                                    $weekNum = array_search($fixture['date'], $dates) + 1;
                                                    ?>
                                                    <span class="text-sm font-bold text-primary">#<?= $weekNum ?></span>
                                                </div>
                                                <div class="flex items-center justify-between bg-surface-hover/20 p-2 rounded">
                                                    <span class="text-xs text-text-muted uppercase font-bold">Live Match</span>
                                                    <label class="relative inline-block w-10 h-5 cursor-pointer">
                                                        <input type="checkbox" name="isLive" value="1" <?= ($fixture['isLive'] ?? 0) ? 'checked' : '' ?> class="sr-only peer">
                                                        <div
                                                            class="w-10 h-5 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors">
                                                        </div>
                                                        <div
                                                            class="absolute left-1 top-1 w-3 h-3 bg-white rounded-full transition-transform peer-checked:translate-x-5">
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </details>
                                    </div>
                                        <div class="pt-0.5 flex items-center gap-3">
                                            <button type="submit" class="btn btn-primary btn-sm px-8">Update</button>
                                            <?php
                                            $fixtureSlug = htmlspecialchars($fixture['homeTeamSlug']) . '-vs-' . htmlspecialchars($fixture['awayTeamSlug']);
                                            ?>
                                            <a href="<?= $basePath ?>/admin/fixture/league/<?= htmlspecialchars($league['slug']) ?>/<?= $fixtureSlug ?>"
                                               class="btn btn-secondary btn-sm">
                                                Edit Details
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <script>
            async function addScorerRow(btn, fieldName) {
                const container = btn.parentElement.nextElementSibling;
                const index = container.children.length + Math.floor(Math.random() * 1000);

                // Get team ID from data attribute
                const details = btn.closest('details');
                const side = fieldName.replace('Scorers', '').toLowerCase();
                const teamId = side === 'home' ? details?.dataset?.homeTeamId : details?.dataset?.awayTeamId;

                if (!teamId) {
                    console.error('Team ID not found');
                    return;
                }

                try {
                    const response = await fetch(`<?= $basePath ?>/admin/leagues/ajax/scorer-row?teamId=${teamId}&side=${side}&index=${index}`);
                    const html = await response.text();

                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    container.appendChild(tempDiv.firstChild);
                } catch (error) {
                    console.error('Failed to load scorer row:', error);
                }
            }

            async function addCardRow(btn, fieldName) {
                const container = btn.parentElement.nextElementSibling;
                const index = container.children.length + Math.floor(Math.random() * 1000);

                // Get team ID from data attribute
                const details = btn.closest('details');
                const side = fieldName.replace('CardsCombined', '').toLowerCase();
                const teamId = side === 'home' ? details?.dataset?.homeTeamId : details?.dataset?.awayTeamId;

                if (!teamId) {
                    console.error('Team ID not found');
                    return;
                }

                try {
                    const response = await fetch(`<?= $basePath ?>/admin/leagues/ajax/card-row?teamId=${teamId}&side=${side}&index=${index}`);
                    const html = await response.text();

                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    container.appendChild(tempDiv.firstChild);
                } catch (error) {
                    console.error('Failed to load card row:', error);
                }
            }
        </script>


    <div class="mt-8 pt-6 border-t border-border">
        <button type="button" onclick="document.getElementById('regenerateModal').showPopover();"
            class="btn bg-orange-500 text-black hover:bg-orange-600 font-bold border-none w-full sm:w-auto">
            Regenerate Fixtures
        </button>
    </div>
</div>