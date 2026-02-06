<div class="">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold m-0 tracking-tight"><?= htmlspecialchars($cup['name']) ?></h1>
            <p class="text-text-muted mt-1 text-lg">Manage Fixtures</p>
        </div>
        <div class="flex gap-4">
            <a href="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>" class="btn btn-secondary">Back to
                Bracket</a>
        </div>
    </div>

    <div class="card mb-8">
        <div class="mb-6 pb-6 border-b border-border">
            <p class="text-text-muted">Enter results for each fixture. Knockout matches support extra time and penalties
                if scores are level.</p>
        </div>

        <?php if (empty($rounds)): ?>
            <div class="text-center py-12 text-text-muted">
                <p>No fixtures scheduled yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($rounds as $round): ?>
                <div class="mb-10 last:mb-0">
                    <h3
                        class="bg-surface-hover/50 py-2 px-4 -mx-6 mb-6 border-y border-border text-center font-bold text-lg text-text-main">
                        <?= htmlspecialchars($round['name']) ?>
                    </h3>

                    <?php foreach ($round['fixtures'] as $fixture): ?>
                        <?php if ($fixture['homeTeamId'] && $fixture['awayTeamId']): ?>
                            <div
                                class="bg-surface border border-border rounded-lg mb-4 p-4 hover:border-primary/30 transition-colors shadow-sm">
                                <form method="POST" action="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/fixtures">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="fixtureId" value="<?= htmlspecialchars($fixture['id']) ?>">

                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center mb-4">
                                        <!-- Home Team -->
                                        <div class="lg:col-span-3 flex items-center justify-end gap-3 text-right order-1 lg:order-1">
                                            <strong
                                                class="text-sm md:text-base"><?= htmlspecialchars($fixture['homeTeamName'] ?? 'TBD') ?></strong>
                                            <span class="inline-block w-4 h-4 rounded-full shrink-0 shadow-sm"
                                                style="background-color: <?= htmlspecialchars($fixture['homeTeamColour'] ?? '#000') ?>;"></span>
                                        </div>

                                        <!-- Scores -->
                                        <div class="lg:col-span-3 flex items-center justify-center gap-2 order-2 lg:order-2">
                                            <input type="number" name="homeScore" min="0" max="99"
                                                class="form-input w-16 text-center font-bold p-1"
                                                value="<?= $fixture['result'] !== null ? htmlspecialchars((string) $fixture['result']['homeScore']) : '' ?>"
                                                placeholder="-">
                                            <span class="text-text-muted font-bold">-</span>
                                            <input type="number" name="awayScore" min="0" max="99"
                                                class="form-input w-16 text-center font-bold p-1"
                                                value="<?= $fixture['result'] !== null ? htmlspecialchars((string) $fixture['result']['awayScore']) : '' ?>"
                                                placeholder="-">
                                        </div>

                                        <!-- Away Team -->
                                        <div class="lg:col-span-3 flex items-center justify-start gap-3 order-3 lg:order-3">
                                            <span class="inline-block w-4 h-4 rounded-full shrink-0 shadow-sm"
                                                style="background-color: <?= htmlspecialchars($fixture['awayTeamColour'] ?? '#000') ?>;"></span>
                                            <strong
                                                class="text-sm md:text-base"><?= htmlspecialchars($fixture['awayTeamName'] ?? 'TBD') ?></strong>
                                        </div>

                                    </div>

                                    <div class="flex justify-between items-start border-t border-border/50 pt-3 mt-3">
                                        <div class="flex flex-col gap-2 flex-1">
                                            <!-- Extra Time / Penalties -->
                                            <details class="group" <?= ($fixture['result'] !== null && (($fixture['result']['extraTime'] ?? false) || ($fixture['result']['penalties'] ?? false))) ? 'open' : '' ?>>
                                                <summary
                                                    class="cursor-pointer text-xs font-medium text-text-muted hover:text-primary transition-colors select-none flex items-center gap-1 w-max">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4 transition-transform group-open:rotate-90" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5l7 7-7 7" />
                                                    </svg>
                                                    Extra Time / Penalties
                                                </summary>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 bg-surface-hover/30 p-4 rounded text-sm">
                                                <?php
                                                // Determine initial toggle state based on existing data
                                                $hasETData = ($fixture['result'] !== null) && (isset($fixture['result']['homeScoreET']) || isset($fixture['result']['awayScoreET']));
                                                $hasPenData = ($fixture['result'] !== null) && (isset($fixture['result']['homePens']) || isset($fixture['result']['awayPens']));
                                                ?>
                                                <!-- Extra Time -->
                                                <div>
                                                    <label class="flex items-center gap-3 mb-2 font-medium cursor-pointer">
                                                        <div class="relative inline-block w-11 h-6">
                                                            <input type="checkbox" name="extraTime" value="1"
                                                                class="sr-only peer"
                                                                id="extraTimeToggle-<?= $fixture['id'] ?>"
                                                                role="switch"
                                                                aria-checked="<?= $hasETData ? 'true' : 'false' ?>"
                                                                aria-labelledby="extraTimeLabel-<?= $fixture['id'] ?>"
                                                                <?= $hasETData ? 'checked' : '' ?>>
                                                            <div class="w-11 h-6 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors peer-focus:ring-2 peer-focus:ring-primary/50"></div>
                                                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                                                        </div>
                                                        <span id="extraTimeLabel-<?= $fixture['id'] ?>">Went to Extra Time</span>
                                                    </label>
                                                    <div class="et-inputs transition-all duration-200"
                                                         id="etInputs-<?= $fixture['id'] ?>"
                                                         style="<?= $hasETData ? '' : 'display: none;' ?>">
                                                        <div class="flex items-center gap-2 ml-6">
                                                            <span class="text-text-muted text-xs">Score after ET:</span>
                                                            <input type="number" name="homeScoreET" min="0" max="99"
                                                                class="form-input w-12 text-center py-1 px-1 text-xs"
                                                                value="<?= $fixture['result']['homeScoreET'] ?? '' ?>" placeholder="-">
                                                            <span class="text-text-muted">-</span>
                                                            <input type="number" name="awayScoreET" min="0" max="99"
                                                                class="form-input w-12 text-center py-1 px-1 text-xs"
                                                                value="<?= $fixture['result']['awayScoreET'] ?? '' ?>" placeholder="-">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Penalties -->
                                                <div>
                                                    <label class="flex items-center gap-3 mb-2 font-medium cursor-pointer">
                                                        <div class="relative inline-block w-11 h-6">
                                                            <input type="checkbox" name="penalties" value="1"
                                                                class="sr-only peer"
                                                                id="penaltiesToggle-<?= $fixture['id'] ?>"
                                                                role="switch"
                                                                aria-checked="<?= $hasPenData ? 'true' : 'false' ?>"
                                                                aria-labelledby="penaltiesLabel-<?= $fixture['id'] ?>"
                                                                <?= $hasPenData ? 'checked' : '' ?>>
                                                            <div class="w-11 h-6 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors peer-focus:ring-2 peer-focus:ring-primary/50"></div>
                                                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                                                        </div>
                                                        <span id="penaltiesLabel-<?= $fixture['id'] ?>">Penalties</span>
                                                    </label>
                                                    <div class="pen-inputs transition-all duration-200"
                                                         id="penInputs-<?= $fixture['id'] ?>"
                                                         style="<?= $hasPenData ? '' : 'display: none;' ?>">
                                                        <div class="flex items-center gap-2 ml-6">
                                                            <span class="text-text-muted text-xs">Penalty Score:</span>
                                                            <input type="number" name="homePens" min="0" max="99"
                                                                class="form-input w-12 text-center py-1 px-1 text-xs"
                                                                value="<?= $fixture['result']['homePens'] ?? '' ?>" placeholder="-">
                                                            <span class="text-text-muted">-</span>
                                                            <input type="number" name="awayPens" min="0" max="99"
                                                                class="form-input w-12 text-center py-1 px-1 text-xs"
                                                                value="<?= $fixture['result']['awayPens'] ?? '' ?>" placeholder="-">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>

                                        <!-- Match Details -->
                                        <details class="group"
                                            data-home-team-id="<?= $fixture['homeTeamId'] ?>"
                                            data-away-team-id="<?= $fixture['awayTeamId'] ?>">
                                            <summary
                                                class="cursor-pointer text-xs font-medium text-text-muted hover:text-primary transition-colors select-none flex items-center gap-1 w-max">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 transition-transform group-open:rotate-90" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                                Match Details (Scorers, Cards)
                                            </summary>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 bg-surface-hover/30 p-4 rounded text-sm">
                                                <?php foreach (['home', 'away'] as $side): ?>
                                                    <div>
                                                        <label class="block mb-2 text-xs uppercase tracking-wide font-semibold text-text-muted border-b border-border/50 pb-1">
                                                            <?= ucfirst($side) ?> Details
                                                        </label>
                                                        
                                                        <!-- Scorers -->
                                                        <div class="mb-4">
                                                            <div class="flex justify-between items-center mb-1">
                                                                <span class="text-xs font-bold">Scorers</span>
                                                                <button type="button" class="text-xs text-primary hover:underline" onclick="addScorerRow(this, '<?= $side ?>Scorers')">+ Add</button>
                                                            </div>
                                                            <div class="scorers-list space-y-1">
                                                                <?php
                                                                if (!is_array($scorers)) $scorers = [];
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
                                                                        <input type="text" name="<?= $side ?>Scorers[<?= $index ?>][minute]" value="<?= htmlspecialchars((string)($scorer['minute'] ?? '')) ?>" class="form-input py-1 px-1 text-xs w-10 text-center" placeholder="Min">
                                                                        <label class="flex items-center gap-1 text-[10px] text-text-muted cursor-pointer whitespace-nowrap">
                                                                            <input type="checkbox" name="<?= $side ?>Scorers[<?= $index ?>][ownGoal]" value="1" <?= ($scorer['ownGoal'] ?? false) ? 'checked' : '' ?>> OG
                                                                        </label>
                                                                        <button type="button" class="text-red-500 hover:text-red-400 text-xs px-1" onclick="this.parentElement.remove()">×</button>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>

                                                        <div class="space-y-2">
                                                            <div class="flex justify-between items-center mb-1">
                                                                <span class="text-xs font-bold">Cards</span>
                                                                <button type="button" class="text-xs text-primary hover:underline"
                                                                    onclick="addCardRow(this, '<?= $side ?>CardsCombined')">+ Add</button>
                                                            </div>
                                                            <div class="cards-list space-y-1">
                                                                <?php 
                                                                $cards = $fixture['result'][$side . 'Cards'] ?? [];
                                                                if (!is_array($cards)) $cards = [];
                                                                $cardTypes = [
                                                                    'sinBins' => 'Sin Bin',
                                                                    'blue' => 'Blue',
                                                                    'yellow' => 'Yellow',
                                                                    'red' => 'Red'
                                                                ];
                                                                $allCards = [];
                                                                foreach ($cards as $typeKey => $typeCards) {
                                                                    if (!is_array($typeCards)) continue;
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
                                                                        <select name="<?= $side ?>CardsCombined[<?= $idx ?>][type]" class="form-input py-1 px-1 text-[10px] w-20">
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
                                                                        <input type="text" name="<?= $side ?>CardsCombined[<?= $idx ?>][minute]" value="<?= htmlspecialchars($card['minute'] ?? '') ?>" class="form-input py-1 px-1 text-xs w-10 text-center" placeholder="Min">
                                                                        <button type="button" class="text-red-500 hover:text-red-400 text-xs px-1" onclick="this.parentElement.remove()">×</button>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>

                                                <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 border-t border-border/50 pt-4 mt-2">
                                                    <!-- Row 1: Date | Referee -->
                                                <div class="flex flex-col">
                                                    <label class="text-[10px] text-text-muted uppercase mb-1">Date</label>
                                                        <input type="date" name="date" value="<?= htmlspecialchars($fixture['date']) ?>" class="form-input py-1 px-2 text-xs">
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <label class="text-[10px] text-text-muted uppercase mb-1">Match Official</label>
                                                        <select name="referee_id" class="form-input py-1 px-2 text-xs">
                                                            <option value="">Not assigned</option>
                                                            <?php foreach ($allStaff as $staff): ?>
                                                                <option value="<?= htmlspecialchars($staff['id']) ?>"
                                                                    <?= ($fixture['refereeId'] ?? null) == $staff['id'] ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($staff['name']) ?>
                                                                    (<?= htmlspecialchars(\App\Models\TeamStaff::formatRole($staff['role'])) ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <!-- Row 2: Time | Pitch -->
                                                    <div class="flex flex-col">
                                                        <label class="text-[10px] text-text-muted uppercase mb-1">Time</label>
                                                        <input type="time" name="time" value="<?= htmlspecialchars(substr($fixture['time'], 0, 5)) ?>" class="form-input py-1 px-2 text-xs">
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <label class="text-[10px] text-text-muted uppercase mb-1">Pitch</label>
                                                        <input type="text" name="pitch" value="<?= htmlspecialchars($fixture['pitch'] ?? '') ?>" class="form-input py-1 px-2 text-xs" placeholder="Pitch">
                                                    </div>

                                                    <!-- Row 3: Week | Live -->
                                                    <div class="flex items-center justify-between bg-surface-hover/20 p-2 rounded">
                                                        <span class="text-[10px] text-text-muted uppercase font-bold">Match Week</span>
                                                        <?php
                                                        // Simple auto-calculation based on round order in cups
                                                        static $cupWeeks = [];
                                                        $roundKey = $round['id'] ?? $round['name'];
                                                        if (!in_array($roundKey, $cupWeeks)) {
                                                            $cupWeeks[] = $roundKey;
                                                        }
                                                        $weekNum = array_search($roundKey, $cupWeeks) + 1;
                                                        ?>
                                                        <span class="text-xs font-bold text-primary">#<?= $weekNum ?></span>
                                                    </div>
                                                    <div class="flex items-center justify-between bg-surface-hover/20 p-2 rounded">
                                                        <span class="text-[10px] text-text-muted uppercase font-bold">Live Match</span>
                                                        <label class="relative inline-block w-10 h-5 cursor-pointer">
                                                            <input type="checkbox" name="isLive" value="1" <?= ($fixture['isLive'] ?? 0) ? 'checked' : '' ?> class="sr-only peer">
                                                            <div class="w-10 h-5 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors"></div>
                                                            <div class="absolute left-1 top-1 w-3 h-3 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </details>
                                        </div>
                                        <div class="pt-0.5">
                                            <button type="submit" class="btn btn-sm btn-primary px-8">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <div
                                class="border border-dashed border-border rounded-lg mb-4 p-8 text-center text-text-muted bg-surface-hover/10">
                                <span class="block mb-1">Match not determined yet</span>
                                <span class="text-xs opacity-75">Waiting for previous round results</span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="mt-8 pt-6 border-t border-border">
            <button type="button"
                onclick="document.getElementById('regenerateModal').showPopover();"
                class="btn btn-primary bg-yellow-600 hover:bg-yellow-700 border-yellow-600 hover:border-yellow-700 text-white w-full sm:w-auto">
                Regenerate Fixtures
            </button>
        </div>
    </div>
</div>

<?php
$modalId = 'regenerateModal';
$competitionType = 'cup';
$formAction = $basePath . '/admin/cups/' . htmlspecialchars($cup['slug'] ?? $cup['id']) . '/regenerate-fixtures';
$competition = $cup;
include __DIR__ . '/../partials/regenerate_modal.php';
?>

<script>
    // Toggle switch show/hide logic
    document.addEventListener('DOMContentLoaded', function() {
        // Handle all ET toggles
        document.querySelectorAll('[id^="extraTimeToggle-"]').forEach(toggle => {
            const fixtureId = toggle.id.replace('extraTimeToggle-', '');
            const inputs = document.getElementById('etInputs-' + fixtureId);

            toggle.addEventListener('change', function() {
                if (this.checked) {
                    inputs.style.display = '';
                } else {
                    inputs.style.display = 'none';
                }
            });
        });

        // Handle all penalty toggles
        document.querySelectorAll('[id^="penaltiesToggle-"]').forEach(toggle => {
            const fixtureId = toggle.id.replace('penaltiesToggle-', '');
            const inputs = document.getElementById('penInputs-' + fixtureId);

            toggle.addEventListener('change', function() {
                if (this.checked) {
                    inputs.style.display = '';
                } else {
                    inputs.style.display = 'none';
                }
            });
        });
    });

    async function addScorerRow(btn, fieldName) {
        const container = btn.parentElement.nextElementSibling;
        const index = container.children.length + Math.floor(Math.random() * 1000);

        const details = btn.closest('details');
        const side = fieldName.replace('Scorers', '').toLowerCase();
        const teamId = side === 'home' ? details?.dataset?.homeTeamId : details?.dataset?.awayTeamId;

        if (!teamId) {
            console.error('Team ID not found');
            return;
        }

        try {
            const response = await fetch(`/admin/cups/ajax/scorer-row?teamId=${teamId}&side=${side}&index=${index}`);
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

        const details = btn.closest('details');
        const side = fieldName.replace('CardsCombined', '').toLowerCase();
        const teamId = side === 'home' ? details?.dataset?.homeTeamId : details?.dataset?.awayTeamId;

        if (!teamId) {
            console.error('Team ID not found');
            return;
        }

        try {
            const response = await fetch(`/admin/cups/ajax/card-row?teamId=${teamId}&side=${side}&index=${index}`);
            const html = await response.text();
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            container.appendChild(tempDiv.firstChild);
        } catch (error) {
            console.error('Failed to load card row:', error);
        }
    }
</script>