<?php
/**
 * Public Cups Page
 */
?>

<div class="w-full">
    <div class="mb-8 text-center">
        <h1
            class="text-4xl font-extrabold m-0 text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400 mb-2">
            Cups</h1>
        <?php if (isset($seasonName) && $seasonName): ?>
            <p class="text-text-muted text-lg font-medium mb-4">
                <?= htmlspecialchars($seasonName) ?> Season
            </p>
        <?php endif; ?>
    </div>

    <?php if (empty($cups)): ?>
        <div class="card">
            <div class="text-center py-12 text-text-muted">
                <p>No cups available for this season</p>
            </div>
        </div>
    <?php else: ?>
        <!-- Cup Selector -->
        <div class="mb-8 max-w-md mx-auto">
            <div class="card p-6">
                <label for="cup-select" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                    Select Cup
                </label>
                <select id="cup-select"
                    class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    <?php foreach ($cups as $cup): ?>
                        <option value="<?= htmlspecialchars($cup['slug']) ?>">
                            <?= htmlspecialchars($cup['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="hidden">
            <div class="card">
                <div class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"></div>
                    <p class="text-text-muted">Loading cup data...</p>
                </div>
            </div>
        </div>

        <!-- Content Container -->
        <div id="cup-content">
            <div id="rounds-container"></div>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden">
            <div class="card">
                <div class="text-center py-12 text-danger">
                    <p id="error-message">Unable to load cup data. Please try again.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    (function () {
        const cupSelect = document.getElementById('cup-select');
        const loadingState = document.getElementById('loading-state');
        const cupContent = document.getElementById('cup-content');
        const errorState = document.getElementById('error-state');
        const roundsContainer = document.getElementById('rounds-container');
        const errorMessage = document.getElementById('error-message');

        if (!cupSelect) return;

        // Load cup data
        async function loadCup(slug) {
            // Show loading state
            cupContent.classList.add('hidden');
            errorState.classList.add('hidden');
            loadingState.classList.remove('hidden');

            try {
                const response = await fetch(`<?= $basePath ?>/cups/${slug}/data`);

                if (!response.ok) {
                    throw new Error('Failed to load cup data');
                }

                const data = await response.json();

                // Render rounds
                renderRounds(data.rounds);

                // Hide loading, show content
                loadingState.classList.add('hidden');
                cupContent.classList.remove('hidden');

                // Save to localStorage
                localStorage.setItem('selectedCupSlug', slug);

            } catch (error) {
                console.error('Error loading cup:', error);
                loadingState.classList.add('hidden');
                errorState.classList.remove('hidden');
                errorMessage.textContent = error.message || 'Unable to load cup data. Please try again.';
            }
        }

        function renderRounds(rounds) {
            if (!rounds || rounds.length === 0) {
                roundsContainer.innerHTML = '<div class="card"><div class="text-center py-12 text-text-muted"><p>No rounds available</p></div></div>';
                return;
            }

            let html = '';

            rounds.forEach(round => {
                html += `
                    <section class="mb-16 last:mb-0">
                        <div class="flex items-center gap-4 mb-6">
                            <h2 class="text-2xl font-bold">${escapeHtml(round.name)}</h2>
                            <div class="h-px bg-border flex-1"></div>
                        </div>
                        <div class="card p-0 overflow-hidden">
                            <div class="divide-y divide-border px-4">
                `;

                if (round.fixtures && round.fixtures.length > 0) {
                    // Group by date if needed, but for cups usually raw list is fine or grouped.
                    // Let's stick to simple list for rounds as they often span days but grouping by date inside a round is nice.
                    // To keep it consistent with leagues, let's group by date.

                    const groups = {};
                    const dates = [];

                    round.fixtures.forEach(f => {
                        const d = f.date || 'TBD';
                        if (!groups[d]) {
                            groups[d] = [];
                            dates.push(d);
                        }
                        groups[d].push(f);
                    });

                    // Sort dates? Assuming API sends them sorted or we rely on JS sort. 
                    // Let's respect API order for now or simple sort
                    dates.sort((a, b) => {
                        if (a === 'TBD') return 1;
                        if (b === 'TBD') return -1;
                        return new Date(a) - new Date(b);
                    });

                    dates.forEach(date => {
                        let dateDisplay = 'TBD';
                        if (date !== 'TBD') {
                            const dateObj = new Date(date + 'T00:00:00');
                            dateDisplay = dateObj.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
                        }

                        html += `
                            <div class="bg-surface/50 -mx-4 border-b border-border py-2 text-center sticky top-0 z-10">
                                <span class="text-xs font-bold text-text-muted uppercase tracking-wider">
                                    ${dateDisplay}
                                </span>
                            </div>
                        `;

                        groups[date].forEach(fixture => {
                            html += renderFixture(fixture);
                        });
                    });

                } else {
                    html += '<div class="text-center py-8 text-text-muted"><p>No fixtures scheduled</p></div>';
                }

                html += `
                            </div>
                        </div>
                    </section>
                `;
            });

            roundsContainer.innerHTML = html;
        }

        // Render single fixture
        function renderFixture(fixture) {
            const homeTeam = fixture.homeTeam || {};
            const awayTeam = fixture.awayTeam || {};
            const result = fixture.result;

            const time = fixture.time || '15:00';
            const homeColour = homeTeam.colour || '#333333';
            const awayColour = awayTeam.colour || '#333333';
            const homeName = escapeHtml(homeTeam.name || 'TBD');
            const awayName = escapeHtml(awayTeam.name || 'TBD');
            const homeId = homeTeam.id || '';
            const awayId = awayTeam.id || '';
            const homeSlug = escapeHtml(homeTeam.slug || homeId);
            const awaySlug = escapeHtml(awayTeam.slug || awayId);

            let scoreHtml = '';
            if (result) {
                const homeScore = parseInt(result.homeScore || 0);
                const awayScore = parseInt(result.awayScore || 0);

                let penaltyHtml = '';
                if (result.penalties) {
                    const pHome = parseInt(result.penalties.homeScore || 0);
                    const pAway = parseInt(result.penalties.awayScore || 0);
                    penaltyHtml = `<div class="text-[10px] text-text-muted font-normal mt-1 text-center">(${pHome} - ${pAway} pens)</div>`;
                }

                scoreHtml = `
                    <div class="flex flex-col items-center">
                        <div class="font-bold text-xl text-primary bg-surface-hover px-3 py-1 rounded-sm">
                            ${homeScore} - ${awayScore}
                        </div>
                        ${penaltyHtml}
                    </div>
                `;
            } else {
                scoreHtml = `<div class="text-base text-text-muted bg-transparent font-medium">${escapeHtml(time)}</div>`;
            }

            const homeLink = homeId ? `<a href="<?= $basePath ?>/team/${homeSlug}" class="hover:text-primary transition-colors">${homeName}</a>` : homeName;
            const awayLink = awayId ? `<a href="<?= $basePath ?>/team/${awaySlug}" class="hover:text-primary transition-colors">${awayName}</a>` : awayName;

            return `
                <div class="flex flex-row items-center py-4 gap-4 hover:bg-surface-hover/50 transition-colors rounded-sm border-b border-border last:border-0 border-opacity-50">
                    <div class="flex-1 flex items-center justify-center gap-4 md:gap-8 w-full">
                        <div class="flex-1 flex items-center justify-end gap-3 font-semibold text-right">
                            ${homeLink}
                            <span class="inline-block w-4 h-4 rounded bg-current shadow-sm" style="color: ${homeColour}; background-color: ${homeColour}"></span>
                        </div>
                        ${scoreHtml}
                        <div class="flex-1 flex items-center justify-start gap-3 font-semibold text-left">
                            <span class="inline-block w-4 h-4 rounded bg-current shadow-sm" style="color: ${awayColour}; background-color: ${awayColour}"></span>
                            ${awayLink}
                        </div>
                    </div>
                </div>
            `;
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Event listener for cup selection
        cupSelect.addEventListener('change', function () {
            loadCup(this.value);
        });

        // On page load: check localStorage or select first
        const savedSlug = localStorage.getItem('selectedCupSlug');
        if (savedSlug) {
            // Check if saved cup exists in options
            const option = Array.from(cupSelect.options).find(opt => opt.value === savedSlug);
            if (option) {
                cupSelect.value = savedSlug;
            }
        }

        // Load the selected cup
        loadCup(cupSelect.value);
    })();
</script>