<div class="max-w-5xl mx-auto">
    <?php $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); ?>
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h1
            class="text-4xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">
            <?= htmlspecialchars($league['name']) ?>
        </h1>
        <div class="flex gap-4">
            <a href="<?= $basePath ?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/edit"
                class="btn btn-secondary">Edit League</a>
        </div>
    </div>

    <!-- Standings -->
    <div class="card mb-8">
        <h2 class="text-xl font-bold mb-6">Standings</h2>
        <?php if (empty($standings)): ?>
            <div class="text-center py-8 text-text-muted">
                <p>No standings data available yet.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr>
                            <th class="table-th w-12 text-center">Pos</th>
                            <th class="table-th text-left">Team</th>
                            <th class="table-th w-12 text-center font-semibold" title="Played">P</th>
                            <th class="table-th w-12 text-center font-semibold" title="Won">W</th>
                            <th class="table-th w-12 text-center font-semibold" title="Drawn">D</th>
                            <th class="table-th w-12 text-center font-semibold" title="Lost">L</th>
                            <th class="table-th w-12 text-center font-semibold hidden sm:table-cell" title="Goals For">GF
                            </th>
                            <th class="table-th w-12 text-center font-semibold hidden sm:table-cell" title="Goals Against">
                                GA</th>
                            <th class="table-th w-12 text-center font-semibold" title="Goal Difference">GD</th>
                            <th class="table-th w-16 text-center font-bold text-text-main">Pts</th>
                            <th class="table-th w-32 text-left hidden md:table-cell">Form</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $position = 1; ?>
                        <?php foreach ($standings as $row): ?>
                            <tr class="hover:bg-surface-hover/50 transition-colors border-b border-border last:border-0">
                                <td class="p-3 text-center text-text-muted"><?= $position++ ?></td>
                                <td class="p-3">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-block w-3 h-3 rounded-sm shadow-sm flex-shrink-0"
                                            style="background-color: <?= htmlspecialchars($row['teamColour']) ?>"></span>
                                        <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($row['teamSlug'] ?? $row['teamId']) ?>"
                                            class="font-semibold text-text-main hover:text-primary transition-colors">
                                            <?= htmlspecialchars($row['teamName']) ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="p-3 text-center text-text-muted"><?= $row['played'] ?></td>
                                <td class="p-3 text-center text-text-muted"><?= $row['won'] ?></td>
                                <td class="p-3 text-center text-text-muted"><?= $row['drawn'] ?></td>
                                <td class="p-3 text-center text-text-muted"><?= $row['lost'] ?></td>
                                <td class="p-3 text-center text-text-muted hidden sm:table-cell"><?= $row['goalsFor'] ?></td>
                                <td class="p-3 text-center text-text-muted hidden sm:table-cell"><?= $row['goalsAgainst'] ?>
                                </td>
                                <td
                                    class="p-3 text-center font-medium <?= $row['goalDifference'] > 0 ? 'text-green-500' : ($row['goalDifference'] < 0 ? 'text-red-500' : 'text-text-muted') ?>">
                                    <?= $row['goalDifference'] > 0 ? '+' . $row['goalDifference'] : $row['goalDifference'] ?>
                                </td>
                                <td class="p-3 text-center font-bold text-lg text-text-main"><?= $row['points'] ?></td>
                                <td class="p-3 hidden md:table-cell">
                                    <div class="flex items-center justify-start gap-1">
                                        <?php if (!empty($row['form'])): ?>
                                            <?php foreach ($row['form'] as $result): ?>
                                                <?php
                                                $colorClass = match ($result) {
                                                    'W' => 'bg-green-500 text-white',
                                                    'D' => 'bg-gray-500 text-white',
                                                    'L' => 'bg-danger text-white',
                                                    default => 'bg-gray-700 text-gray-300'
                                                };
                                                ?>
                                                <span
                                                    class="w-5 h-5 flex items-center justify-center rounded text-[10px] font-bold <?= $colorClass ?>"
                                                    title="<?= $result ?>">
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
    </div>

    <!-- Recent Results -->
    <div class="card mb-8">
        <h2 class="text-xl font-bold mb-6 pb-4 border-b border-border">Recent Results</h2>
        <?php
        $playedFixtures = array_filter($fixtures, fn($f) => !empty($f['result']));
        $playedFixtures = array_slice(array_reverse($playedFixtures), 0, 10);
        ?>
        <?php if (empty($playedFixtures)): ?>
            <div class="text-center py-8 text-text-muted">
                <p>No results recorded yet.</p>
            </div>
        <?php else: ?>
            <?php
            $groupedResults = [];
            foreach ($playedFixtures as $f) {
                $groupedResults[$f['date']][] = $f;
            }
            ?>
            <div class="flex flex-col">
                <?php foreach ($groupedResults as $date => $fixtures): ?>
                    <div class="bg-surface/50 border-b border-border py-2 text-center sticky top-0 z-10">
                        <span class="text-xs font-bold text-text-muted uppercase tracking-wider">
                            <?= date('D j M', strtotime($date)) ?>
                        </span>
                    </div>
                    <div class="divide-y divide-border border-b border-border last:border-0 px-4">
                        <?php foreach ($fixtures as $fixture): ?>
                            <div class="flex items-center py-3 gap-2" id="result-<?= htmlspecialchars($fixture['id']) ?>">
                                <div class="flex-1 flex items-center justify-end gap-2 text-right">
                                    <span
                                        class="font-medium text-sm truncate"><?= htmlspecialchars($fixture['homeTeamName']) ?></span>
                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                        style="background-color: <?= htmlspecialchars($fixture['homeTeamColour']) ?>"></span>
                                </div>

                                <!-- Score Display Mode -->
                                <div class="score-display flex items-center gap-2">
                                    <div class="bg-surface-hover px-2 py-1 rounded text-xs font-bold text-text-main">
                                        <span class="home-score"><?= $fixture['result']['homeScore'] ?></span> - <span
                                            class="away-score"><?= $fixture['result']['awayScore'] ?></span>
                                    </div>
                                    <button type="button"
                                        onclick="editResult('<?= htmlspecialchars($fixture['id']) ?>', <?= $fixture['result']['homeScore'] ?>, <?= $fixture['result']['awayScore'] ?>)"
                                        class="p-1.5 hover:bg-surface-hover rounded transition-colors" title="Edit Result">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-muted hover:text-primary"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Score Edit Mode (hidden by default) -->
                                <form class="score-edit hidden items-center gap-2"
                                    onsubmit="saveResult(event, '<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>', '<?= htmlspecialchars($fixture['id']) ?>')">
                                    <input type="number" name="homeScore" min="0" max="99"
                                        class="w-10 text-center p-1 rounded bg-surface border border-border focus:border-primary focus:outline-none text-xs font-bold"
                                        value="<?= $fixture['result']['homeScore'] ?>" required>
                                    <span class="text-text-muted font-bold text-xs">:</span>
                                    <input type="number" name="awayScore" min="0" max="99"
                                        class="w-10 text-center p-1 rounded bg-surface border border-border focus:border-primary focus:outline-none text-xs font-bold"
                                        value="<?= $fixture['result']['awayScore'] ?>" required>
                                    <button type="submit"
                                        class="p-1 rounded bg-primary text-white hover:bg-primary-hover shadow-sm transition-colors"
                                        title="Save Result">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 21v-8H7v8M7 3v5h8" />
                                        </svg>
                                    </button>
                                </form>

                                <div class="flex-1 flex items-center justify-start gap-2 text-left">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                        style="background-color: <?= htmlspecialchars($fixture['awayTeamColour']) ?>"></span>
                                    <span
                                        class="font-medium text-sm truncate"><?= htmlspecialchars($fixture['awayTeamName']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Upcoming Fixtures -->
    <div class="card p-0">
        <div class="flex items-center justify-between p-6 border-b border-border bg-surface/50">
            <h2 class="text-xl font-bold m-0">Upcoming Fixtures</h2>
            <a href="<?= $basePath ?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/fixtures"
                class="btn btn-primary btn-sm">Manage Fixtures</a>
        </div>
        <?php
        $upcomingFixtures = array_filter($fixtures, fn($f) => empty($f['result']));
        $upcomingFixtures = array_slice($upcomingFixtures, 0, 10);
        $today = date('Y-m-d');
        ?>
        <?php if (empty($upcomingFixtures)): ?>
            <div class="text-center py-8 text-text-muted">
                <p>No upcoming fixtures.</p>
            </div>
        <?php else: ?>
            <?php
            $groupedUpcoming = [];
            foreach ($upcomingFixtures as $f) {
                $groupedUpcoming[$f['date']][] = $f;
            }
            ?>
            <div class="flex flex-col">
                <?php foreach ($groupedUpcoming as $date => $fixtures): ?>
                    <div class="bg-surface/50 border-b border-border py-2 text-center sticky top-0 z-10">
                        <span class="text-xs font-bold text-text-muted uppercase tracking-wider">
                            <?= date('D j M', strtotime($date)) ?>
                        </span>
                    </div>
                    <div class="divide-y divide-border border-b border-border last:border-0 px-4">
                        <?php foreach ($fixtures as $fixture): ?>
                            <?php $canEnterScore = $fixture['date'] <= $today; ?>
                            <div class="flex items-center py-3 gap-2">
                                <div class="flex-1 flex items-center justify-end gap-2 text-right">
                                    <span
                                        class="font-medium text-sm truncate"><?= htmlspecialchars($fixture['homeTeamName']) ?></span>
                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                        style="background-color: <?= htmlspecialchars($fixture['homeTeamColour']) ?>"></span>
                                </div>
                                <?php if ($canEnterScore): ?>
                                    <form
                                        onsubmit="saveLeagueScore(event, '<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>', '<?= htmlspecialchars($fixture['id']) ?>')"
                                        class="flex items-center gap-2">
                                        <input type="number" name="homeScore" min="0" max="99"
                                            class="w-10 text-center p-1 rounded bg-surface border border-border focus:border-primary focus:outline-none text-xs font-bold"
                                            placeholder="-" required>
                                        <span class="text-text-muted font-bold text-xs">:</span>
                                        <input type="number" name="awayScore" min="0" max="99"
                                            class="w-10 text-center p-1 rounded bg-surface border border-border focus:border-primary focus:outline-none text-xs font-bold"
                                            placeholder="-" required>
                                        <button type="submit"
                                            class="p-1 rounded bg-primary text-white hover:bg-primary-hover shadow-sm transition-colors"
                                            title="Save Result">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="bg-surface-hover px-2 py-1 rounded text-xs font-medium text-text-muted">
                                        <?= $fixture['time'] ?>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1 flex items-center justify-start gap-2 text-left">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                        style="background-color: <?= htmlspecialchars($fixture['awayTeamColour']) ?>"></span>
                                    <span
                                        class="font-medium text-sm truncate"><?= htmlspecialchars($fixture['awayTeamName']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const CSRF_TOKEN = '<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>';
    const BASE_PATH = '<?= $basePath ?>';

    function editResult(fixtureId, currentHomeScore, currentAwayScore) {
        const resultRow = document.getElementById(`result-${fixtureId}`);
        const displayMode = resultRow.querySelector('.score-display');
        const editMode = resultRow.querySelector('.score-edit');

        // Hide display, show edit
        displayMode.classList.add('hidden');
        displayMode.classList.remove('flex');
        editMode.classList.remove('hidden');
        editMode.classList.add('flex');

        // Focus first input
        editMode.querySelector('input[name="homeScore"]').focus();
    }

    function saveResult(event, leagueSlug, fixtureId) {
        event.preventDefault();
        const form = event.target;
        const btn = form.querySelector('button');
        const originalContent = btn.innerHTML;

        const homeScore = form.homeScore.value;
        const awayScore = form.awayScore.value;

        if (homeScore === '' || awayScore === '') return;

        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full"></span>';

        const formData = new FormData();
        formData.append('fixtureId', fixtureId);
        formData.append('homeScore', homeScore);
        formData.append('awayScore', awayScore);
        formData.append('csrf_token', CSRF_TOKEN);
        formData.append('ajax', '1');

        fetch(`${BASE_PATH}/admin/leagues/${leagueSlug}/fixtures`, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.redirected) return { success: true };
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch {
                        return { success: true };
                    }
                });
            })
            .then(data => {
                // Update the display with new scores
                const resultRow = document.getElementById(`result-${fixtureId}`);
                resultRow.querySelector('.home-score').textContent = homeScore;
                resultRow.querySelector('.away-score').textContent = awayScore;

                // Update the onclick handler with new values
                const displayMode = resultRow.querySelector('.score-display');
                const editBtn = displayMode.querySelector('button');
                editBtn.onclick = () => editResult(fixtureId, homeScore, awayScore);

                // Switch back to display mode
                displayMode.classList.remove('hidden');
                displayMode.classList.add('flex');
                const editMode = resultRow.querySelector('.score-edit');
                editMode.classList.add('hidden');
                editMode.classList.remove('flex');

                // Show success briefly
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                btn.classList.add('bg-green-600');

                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    btn.classList.remove('bg-green-600');
                }, 1000);

                // Reload page after delay to update standings table
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalContent;
                alert('Failed to save score. Please try again.');
            });
    }

    function saveLeagueScore(event, leagueSlug, fixtureId) {
        event.preventDefault();
        const form = event.target;
        const btn = form.querySelector('button');
        const originalContent = btn.innerHTML;

        const homeScore = form.homeScore.value;
        const awayScore = form.awayScore.value;

        if (homeScore === '' || awayScore === '') return;

        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full"></span>';

        const formData = new FormData();
        formData.append('fixtureId', fixtureId);
        formData.append('homeScore', homeScore);
        formData.append('awayScore', awayScore);
        formData.append('csrf_token', CSRF_TOKEN);
        formData.append('ajax', '1');

        fetch(`${BASE_PATH}/admin/leagues/${leagueSlug}/fixtures`, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.redirected) return { success: true };
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch {
                        return { success: true };
                    }
                });
            })
            .then(data => {
                // Show success state
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                btn.classList.add('bg-green-600');

                // Reload page after short delay to show updated standings
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalContent;
                alert('Failed to save score. Please try again.');
            });
    }
</script>