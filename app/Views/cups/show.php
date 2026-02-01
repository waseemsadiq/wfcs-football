<div class="max-w-full mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <h1
            class="text-4xl font-extrabold tracking-tight text-text-main">
            <?= htmlspecialchars($cup['name']) ?>
        </h1>
        <div class="flex gap-4">
            <a href="<?= $basePath ?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/edit"
                class="btn btn-secondary">Edit Cup</a>
        </div>
    </div>

    <!-- Tournament Bracket -->
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-border">
            <h2 class="text-xl font-bold">Tournament Bracket</h2>
            <div class="flex gap-2 items-center text-sm text-text-muted">
                <span class="flex-1"><span class="font-bold">Key:</span> Scores in () = extra time, [] =
                    penalties</span>
                <a href="<?= $basePath ?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/fixtures"
                    class="btn btn-primary mr-3">Manage Fixtures</a>
            </div>
        </div>

        <?php if (empty($rounds)): ?>
            <div class="text-center py-16 text-text-muted">
                <p>No bracket generated yet.</p>
                <p class="text-sm mt-2">Add teams and regenerate fixtures to see the bracket.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto pb-4 custom-scrollbar">
                <div class="flex gap-12 min-w-max px-4">
                    <?php foreach ($rounds as $roundIndex => $round): ?>
                        <div class="flex flex-col min-w-[260px]">
                            <h3 class="text-center mb-6 text-sm font-bold text-text-muted uppercase tracking-wider">
                                <?= htmlspecialchars($round['name']) ?>
                            </h3>
                            <div class="flex flex-col justify-around flex-grow w-full gap-4">
                                <?php
                                $today = date('Y-m-d');
                                foreach ($round['fixtures'] as $fixture):
                                    ?>
                                    <div
                                        class="border border-border rounded-lg bg-surface shadow-sm overflow-hidden relative group hover:border-primary/30 transition-colors">
                                        <?php
                                        $hasResult = !empty($fixture['result']);
                                        $bothTeamsDetermined = !empty($fixture['homeTeamId']) && !empty($fixture['awayTeamId'])
                                            && ($fixture['homeTeamName'] ?? 'TBD') !== 'TBD'
                                            && ($fixture['awayTeamName'] ?? 'TBD') !== 'TBD';
                                        $canEnterScore = !$hasResult && ($fixture['date'] ?? '') <= $today && $bothTeamsDetermined;
                                        $homeWon = $hasResult && (
                                            ($fixture['result']['winnerId'] ?? '') === 'home' ||
                                            (!isset($fixture['result']['winnerId']) && ($fixture['result']['homeScore'] ?? 0) > ($fixture['result']['awayScore'] ?? 0))
                                        );
                                        $awayWon = $hasResult && (
                                            ($fixture['result']['winnerId'] ?? '') === 'away' ||
                                            (!isset($fixture['result']['winnerId']) && ($fixture['result']['awayScore'] ?? 0) > ($fixture['result']['homeScore'] ?? 0))
                                        );
                                        ?>
                                        <?php if ($canEnterScore): ?>
                                            <!-- Score Entry Form for Cup Match -->
                                            <form
                                                onsubmit="saveCupScore(event, '<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>', '<?= htmlspecialchars($fixture['id']) ?>', '<?= htmlspecialchars($round['name']) ?>')"
                                                class="p-3" id="cup-form-<?= htmlspecialchars($fixture['id']) ?>">
                                                <!-- Home Team -->
                                                <div class="flex items-center mb-2 pb-2 border-b border-border/50">
                                                    <span class="inline-block w-3 h-3 rounded-sm flex-shrink-0 mr-2"
                                                        style="background-color: <?= htmlspecialchars($fixture['homeTeamColour']) ?>;"></span>
                                                    <span
                                                        class="flex-grow truncate text-sm"><?= htmlspecialchars($fixture['homeTeamName']) ?></span>
                                                    <input type="number" name="homeScore" min="0" max="99"
                                                        class="w-12 text-center p-1 rounded bg-surface border border-border focus:border-primary focus:outline-none text-sm font-bold ml-2"
                                                        placeholder="0" required>
                                                </div>

                                                <!-- Away Team -->
                                                <div class="flex items-center mb-3">
                                                    <span class="inline-block w-3 h-3 rounded-sm flex-shrink-0 mr-2"
                                                        style="background-color: <?= htmlspecialchars($fixture['awayTeamColour']) ?>;"></span>
                                                    <span
                                                        class="flex-grow truncate text-sm"><?= htmlspecialchars($fixture['awayTeamName']) ?></span>
                                                    <input type="number" name="awayScore" min="0" max="99"
                                                        class="w-12 text-center p-1 rounded bg-surface border border-border focus:border-primary focus:outline-none text-sm font-bold ml-2"
                                                        placeholder="0" required>
                                                </div>

                                                <!-- Save Button -->
                                                <button type="submit"
                                                    class="w-full py-2 rounded bg-primary text-white hover:bg-primary-hover shadow-sm transition-colors text-sm font-medium flex items-center justify-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Save Result
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <!-- Home Team -->
                                            <div
                                                class="flex items-center px-3 py-2 border-b border-border/50 <?= $homeWon ? 'bg-primary/10 font-bold' : '' ?>">
                                                <span class="inline-block w-3 h-3 rounded-sm flex-shrink-0 mr-2"
                                                    style="background-color: <?= htmlspecialchars($fixture['homeTeamColour']) ?>;"></span>
                                                <span
                                                    class="flex-grow truncate text-sm"><?= htmlspecialchars($fixture['homeTeamName']) ?></span>
                                                <?php if ($hasResult): ?>
                                                    <span class="font-bold text-sm ml-2">
                                                        <?= $fixture['result']['homeScore'] ?>
                                                        <?php if ($fixture['result']['extraTime'] ?? false): ?>
                                                            <span
                                                                class="text-xs text-text-muted font-normal">(<?= $fixture['result']['homeScoreET'] ?? '' ?>)</span>
                                                        <?php endif; ?>
                                                        <?php if ($fixture['result']['penalties'] ?? false): ?>
                                                            <span
                                                                class="text-xs text-text-muted font-normal">[<?= $fixture['result']['homePens'] ?? '' ?>]</span>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Away Team -->
                                            <div class="flex items-center px-3 py-2 <?= $awayWon ? 'bg-primary/10 font-bold' : '' ?>">
                                                <span class="inline-block w-3 h-3 rounded-sm flex-shrink-0 mr-2"
                                                    style="background-color: <?= htmlspecialchars($fixture['awayTeamColour']) ?>;"></span>
                                                <span
                                                    class="flex-grow truncate text-sm"><?= htmlspecialchars($fixture['awayTeamName']) ?></span>
                                                <?php if ($hasResult): ?>
                                                    <span class="font-bold text-sm ml-2">
                                                        <?= $fixture['result']['awayScore'] ?>
                                                        <?php if ($fixture['result']['extraTime'] ?? false): ?>
                                                            <span
                                                                class="text-xs text-text-muted font-normal">(<?= $fixture['result']['awayScoreET'] ?? '' ?>)</span>
                                                        <?php endif; ?>
                                                        <?php if ($fixture['result']['penalties'] ?? false): ?>
                                                            <span
                                                                class="text-xs text-text-muted font-normal">[<?= $fixture['result']['awayPens'] ?? '' ?>]</span>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($fixture['date']): ?>
                                            <div
                                                class="absolute top-0 right-0 p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <div class="bg-surface-hover border border-border text-xs px-1.5 rounded shadow-sm">
                                                    <?= date('d M', strtotime($fixture['date'])) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const CSRF_TOKEN = '<?= htmlspecialchars(\Core\Auth::csrfToken()) ?>';

    function saveCupScore(event, cupSlug, fixtureId, roundName) {
        event.preventDefault();
        const form = event.target;
        const btn = form.querySelector('button');
        const originalContent = btn.innerHTML;

        const homeScore = form.homeScore.value;
        const awayScore = form.awayScore.value;

        if (homeScore === '' || awayScore === '') return;

        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span> Saving...';

        const formData = new FormData();
        formData.append('fixtureId', fixtureId);
        formData.append('homeScore', homeScore);
        formData.append('awayScore', awayScore);
        formData.append('csrf_token', CSRF_TOKEN);
        formData.append('ajax', '1');

        fetch(`<?= $basePath ?>/admin/cups/${cupSlug}/fixtures`, {
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
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Saved!';
                btn.classList.remove('bg-primary');
                btn.classList.add('bg-green-600');

                // Reload page after short delay to show updated bracket
                setTimeout(() => {
                    window.location.reload();
                }, 1200);
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalContent;
                alert('Failed to save score. Please try again.');
            });
    }
</script>