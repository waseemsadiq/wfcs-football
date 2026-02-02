<div class="">
    <?php $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); ?>
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h1
            class="text-4xl font-extrabold tracking-tight text-text-main">
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
        <?php
        $context = 'admin';
        include __DIR__ . '/../partials/standings_table.php';
        ?>
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
                <?php foreach ($groupedResults as $date => $dateFixtures): ?>
                    <div class="bg-surface/50 border-b border-border py-2 text-center sticky top-0 z-10">
                        <span class="text-xs font-bold text-text-muted uppercase tracking-wider">
                            <?= date('D j M', strtotime($date)) ?>
                        </span>
                    </div>
                    <div class="divide-y divide-border border-b border-border last:border-0 px-4">
                        <?php foreach ($dateFixtures as $fixture): ?>
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
                                        onclick="editResult('<?= htmlspecialchars($fixture['id']) ?>')"
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
                                        value="<?= $fixture['result']['homeScore'] ?>" required aria-required="true">
                                    <span class="text-text-muted font-bold text-xs">:</span>
                                    <input type="number" name="awayScore" min="0" max="99"
                                        class="w-10 text-center p-1 rounded bg-surface border border-border focus:border-primary focus:outline-none text-xs font-bold"
                                        value="<?= $fixture['result']['awayScore'] ?>" required aria-required="true">
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
    <?php
    $upcomingFixtures = array_filter($fixtures, function ($f) {
        return !isset($f['result']) || $f['result'] === null || empty($f['result']);
    });
    $today = date('Y-m-d');
    ?>
    <div class="card p-0">
        <div class="flex items-center justify-between p-6 border-b border-border bg-surface/50">
            <h2 class="text-xl font-bold m-0">Upcoming Fixtures</h2>
            <a href="<?= $basePath ?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/fixtures"
                class="btn btn-primary btn-sm">Manage Fixtures</a>
        </div>
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
                                            placeholder="-" required aria-required="true">
                                        <span class="text-text-muted font-bold text-xs">:</span>
                                        <input type="number" name="awayScore" min="0" max="99"
                                            class="w-10 text-center p-1 rounded bg-surface border border-border focus:border-primary focus:outline-none text-xs font-bold"
                                            placeholder="-" required aria-required="true">
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
    const SPINNER_HTML = '<span class="animate-spin inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full"></span>';
    const CHECK_SVG = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';

    function editResult(fixtureId) {
        const resultRow = document.getElementById(`result-${fixtureId}`);
        const displayMode = resultRow.querySelector('.score-display');
        const editMode = resultRow.querySelector('.score-edit');

        displayMode.classList.add('hidden');
        displayMode.classList.remove('flex');
        editMode.classList.remove('hidden');
        editMode.classList.add('flex');
        editMode.querySelector('input[name="homeScore"]').focus();
    }

    function saveScore(event, leagueSlug, fixtureId, updateDisplay) {
        event.preventDefault();
        const form = event.target;
        const btn = form.querySelector('button');
        const originalContent = btn.innerHTML;

        const homeScore = form.homeScore.value;
        const awayScore = form.awayScore.value;

        if (homeScore === '' || awayScore === '') return;

        btn.disabled = true;
        btn.innerHTML = SPINNER_HTML;

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
            .then(() => {
                if (updateDisplay) {
                    const resultRow = document.getElementById(`result-${fixtureId}`);
                    resultRow.querySelector('.home-score').textContent = homeScore;
                    resultRow.querySelector('.away-score').textContent = awayScore;

                    const displayMode = resultRow.querySelector('.score-display');
                    const editBtn = displayMode.querySelector('button');
                    editBtn.onclick = () => editResult(fixtureId);

                    displayMode.classList.remove('hidden');
                    displayMode.classList.add('flex');
                    resultRow.querySelector('.score-edit').classList.add('hidden');
                    resultRow.querySelector('.score-edit').classList.remove('flex');
                }

                btn.innerHTML = CHECK_SVG;
                btn.classList.add('bg-blue-600');

                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    btn.classList.remove('bg-blue-600');
                }, 1000);

                setTimeout(() => window.location.reload(), updateDisplay ? 2000 : 1000);
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalContent;
                alert('Failed to save score. Please try again.');
            });
    }

    function saveResult(event, leagueSlug, fixtureId) {
        saveScore(event, leagueSlug, fixtureId, true);
    }

    function saveLeagueScore(event, leagueSlug, fixtureId) {
        saveScore(event, leagueSlug, fixtureId, false);
    }
</script>