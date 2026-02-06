<?php
/**
 * Public Cups Page
 */
?>

<div class="w-full">
    <?php
    $title = 'Cups';
    $subtitle = (isset($seasonName) && $seasonName) ? htmlspecialchars($seasonName) . ' Season' : null;
    include __DIR__ . '/../partials/page_header.php';
    ?>

    <?php if (empty($cups)): ?>
        <div class="card">
            <div class="text-center py-12 text-text-muted">
                <p>No cups available for this season</p>
            </div>
        </div>
    <?php else: ?>
        <!-- Cup Selector and View Toggle -->
        <div class="mb-8 max-w-md mx-auto">
            <div class="card p-6">
                <label for="cup-select" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                    Select Cup
                </label>
                <div class="flex gap-4 items-center">
                    <!-- Cup Selector -->
                    <select id="cup-select"
                        class="flex-1 bg-surface border border-border text-text-main rounded-sm px-4 py-3 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <?php foreach ($cups as $cup): ?>
                            <option value="<?= htmlspecialchars($cup['slug']) ?>">
                                <?= htmlspecialchars($cup['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>


                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="hidden">
            <div class="card">
                <div class="text-center py-12" role="status" aria-live="polite">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"
                        aria-hidden="true"></div>
                    <p class="text-text-muted">Loading cup data...</p>
                </div>
            </div>
        </div>

        <!-- Bracket View -->
        <div id="bracket-view" class="hidden">
            <div id="bracket-container"></div>
        </div>

        <!-- Fixtures View -->
        <div id="fixtures-view" class="hidden">
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
        const errorState = document.getElementById('error-state');
        const roundsContainer = document.getElementById('rounds-container');
        const errorMessage = document.getElementById('error-message');

        if (!cupSelect) return;

        // View mode management (global for onclick handlers)
        window.setViewMode = function (mode) {
            const bracketView = document.getElementById('bracket-view');
            const fixturesView = document.getElementById('fixtures-view');
            if (mode === 'bracket') {
                // Show bracket, hide fixtures
                bracketView.classList.remove('hidden');
                fixturesView.classList.add('hidden');
            } else if (mode === 'fixtures') {
                // Show fixtures, hide bracket
                fixturesView.classList.remove('hidden');
                bracketView.classList.add('hidden');
            }

            // Save preference to localStorage
            try {
                localStorage.setItem('cupViewMode', mode);
            } catch (e) {
                // localStorage disabled or quota exceeded - gracefully ignore
                console.warn('Could not save view preference:', e);
            }
        }

        // Render bracket view (all content sanitized via escapeHtml before innerHTML)
        function renderBracket(rounds, cupName) {
            const bracketContainer = document.getElementById('bracket-container');

            if (!rounds || rounds.length === 0) {
                bracketContainer.innerHTML = '<div class="card"><div class="text-center py-16 text-text-muted"><p>No bracket generated yet.</p></div></div>';
                return;
            }

            let html = `
                <div class="card p-0 overflow-hidden">
                    <div class="p-6 border-b border-border bg-surface/50 flex justify-between items-center">
                        <h2 class="text-xl font-bold m-0">Tournament Bracket</h2>
                        <button onclick="setViewMode('fixtures')" class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-sm text-sm font-bold transition-colors">
                            Fixtures
                        </button>
                    </div>
                    <div class="overflow-x-auto pb-4 pt-6">
                        <div class="flex gap-12 min-w-max px-4">
            `;

            rounds.forEach(round => {
                let roundDateDisplay = '';
                if (round.fixtures && round.fixtures.length > 0) {
                    for (const f of round.fixtures) {
                        if (f.date) {
                            const dateObj = new Date(f.date + 'T00:00:00');
                            // Format: 31 Jan
                            roundDateDisplay = ' (' + dateObj.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' }) + ')';
                            break;
                        }
                    }
                }

                html += `
                    <div class="flex flex-col min-w-[260px]">
                        <h3 class="text-center mb-6 text-sm font-bold text-text-muted uppercase tracking-wider">
                            ${escapeHtml(round.name)}${roundDateDisplay}
                        </h3>
                        <div class="flex flex-col justify-around flex-grow w-full gap-4">
                `;

                if (round.fixtures && round.fixtures.length > 0) {
                    round.fixtures.forEach(fixture => {
                        html += renderBracketFixture(fixture);
                    });
                } else {
                    html += '<div class="text-center py-8 text-text-muted text-sm">No fixtures</div>';
                }

                html += `
                        </div>
                    </div>
                `;
            });

            html += '</div></div></div>';
            bracketContainer.innerHTML = html;
        }

        // Render single fixture for bracket view (all dynamic content sanitized via escapeHtml)
        function renderBracketFixture(fixture) {
            const homeTeam = fixture.homeTeam || {};
            const awayTeam = fixture.awayTeam || {};
            const result = fixture.result;

            const homeColour = homeTeam.colour || '#333333';
            const awayColour = awayTeam.colour || '#333333';
            const homeName = escapeHtml(homeTeam.name || 'TBD');
            const awayName = escapeHtml(awayTeam.name || 'TBD');
            const homeId = homeTeam.id || '';
            const awayId = awayTeam.id || '';
            const homeSlug = escapeHtml(homeTeam.slug || homeId);
            const awaySlug = escapeHtml(awayTeam.slug || awayId);

            // Determine winner
            let homeWon = false;
            let awayWon = false;
            if (result) {
                const homeScore = parseInt(result.homeScore || 0);
                const awayScore = parseInt(result.awayScore || 0);

                if (result.penalties) {
                    const pHome = parseInt(result.homePens || 0);
                    const pAway = parseInt(result.awayPens || 0);
                    homeWon = pHome > pAway;
                    awayWon = pAway > pHome;
                } else {
                    homeWon = homeScore > awayScore;
                    awayWon = awayScore > homeScore;
                }
            }

            const homeLink = homeId ? `<a href="<?= $basePath ?>/team/${homeSlug}" class="hover:text-primary transition-colors">${homeName}</a>` : homeName;
            const awayLink = awayId ? `<a href="<?= $basePath ?>/team/${awaySlug}" class="hover:text-primary transition-colors">${awayName}</a>` : awayName;

            let dateTooltip = '';
            if (fixture.date) {
                const dateObj = new Date(fixture.date + 'T00:00:00');
                const dateStr = dateObj.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
                dateTooltip = `
                    <div class="absolute top-0 right-0 p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="bg-surface-hover border border-border text-xs px-1.5 rounded shadow-sm">
                            ${dateStr}
                        </div>
                    </div>
                `;
            }

            return `
                <div class="border border-border rounded-lg bg-surface shadow-sm overflow-hidden relative group hover:border-primary/30 transition-colors">
                    <!-- Home Team -->
                    <div class="flex items-center px-3 py-2 border-b border-border/50 ${homeWon ? 'bg-primary/10 font-bold' : ''}">
                        <span class="inline-block w-3 h-3 rounded-full flex-shrink-0 mr-2"
                            style="background-color: ${homeColour};"></span>
                        <span class="flex-grow truncate text-sm">${homeLink}</span>
                        ${result ? `<span class="font-bold text-sm ml-2">${result.homeScore}${result.extraTime ? ' (' + result.homeScoreET + ')' : ''}${result.penalties ? ' [' + result.homePens + ']' : ''}</span>` : ''}
                    </div>

                    <!-- Away Team -->
                    <div class="flex items-center px-3 py-2 ${awayWon ? 'bg-primary/10 font-bold' : ''}">
                        <span class="inline-block w-3 h-3 rounded-full flex-shrink-0 mr-2"
                            style="background-color: ${awayColour};"></span>
                        <span class="flex-grow truncate text-sm">${awayLink}</span>
                        ${result ? `<span class="font-bold text-sm ml-2">${result.awayScore}${result.extraTime ? ' (' + result.awayScoreET + ')' : ''}${result.penalties ? ' [' + result.awayPens + ']' : ''}</span>` : ''}
                    </div>

                    ${dateTooltip}
                </div>
            `;
        }

        // Load cup data
        async function loadCup(slug) {
            // Show loading state
            document.getElementById('bracket-view').classList.add('hidden');
            document.getElementById('fixtures-view').classList.add('hidden');
            errorState.classList.add('hidden');
            loadingState.classList.remove('hidden');

            try {
                const response = await fetch(`<?= $basePath ?>/cups/${slug}/data`);

                if (!response.ok) {
                    throw new Error('Failed to load cup data');
                }

                const data = await response.json();

                // Render both views
                renderBracket(data.rounds, data.cup.name);
                renderRounds(data.rounds);

                // Determine which view to show
                let viewMode = 'bracket'; // default
                try {
                    const saved = localStorage.getItem('cupViewMode');
                    if (saved === 'fixtures' || saved === 'bracket') {
                        viewMode = saved;
                    }
                } catch (e) {
                    // localStorage disabled - use default
                }

                // Apply view mode
                setViewMode(viewMode);

                // Hide loading
                loadingState.classList.add('hidden');

                // Save to localStorage
                localStorage.setItem('selectedCupSlug', slug);

            } catch (error) {
                console.error('Error loading cup:', error);
                loadingState.classList.add('hidden');
                document.getElementById('bracket-view').classList.add('hidden');
                document.getElementById('fixtures-view').classList.add('hidden');
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

            rounds.forEach((round, index) => {
                let headerContent = `<h2 class="text-xl font-bold m-0">${escapeHtml(round.name)}</h2>`;

                // If first round, add the View Bracket button
                if (index === 0) {
                    headerContent = `
                        <div class="flex justify-between items-center w-full">
                            <h2 class="text-xl font-bold m-0">${escapeHtml(round.name)}</h2>
                            <button onclick="setViewMode('bracket')" class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-sm text-sm font-bold transition-colors">
                                Bracket
                            </button>
                        </div>
                    `;
                }

                html += `
                    <section class="mb-16 last:mb-0">
                        <div class="card p-0 overflow-hidden">
                            <div class="p-6 border-b border-border bg-surface/50">
                                ${headerContent}
                            </div>
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
                            <div class="bg-surface border-l-4 border-l-primary border-b border-border py-2 text-center -mx-4 px-4 sticky top-0 z-10">
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

            // Strip seconds from time (HH:MM:SS -> HH:MM)
            const time = (fixture.time || '15:00').substring(0, 5);
            const homeColour = homeTeam.colour || '#333333';
            const awayColour = awayTeam.colour || '#333333';
            const homeName = escapeHtml(homeTeam.name || 'TBD');
            const awayName = escapeHtml(awayTeam.name || 'TBD');
            const homeId = homeTeam.id || '';
            const awayId = awayTeam.id || '';
            const homeSlug = escapeHtml(homeTeam.slug || homeId);
            const awaySlug = escapeHtml(awayTeam.slug || awayId);

            // Generate fixture detail link
            const fixtureSlug = `${homeSlug}-vs-${awaySlug}`;
            const competitionSlug = fixture.competitionSlug || '';
            const fixtureDetailUrl = competitionSlug ? `<?= $basePath ?>/fixture/cup/${competitionSlug}/${fixtureSlug}` : '';

            let scoreHtml = '';
            if (result) {
                const homeScore = parseInt(result.homeScore || 0);
                const awayScore = parseInt(result.awayScore || 0);

                let extraInfoHtml = '';
                if (result.extraTime || result.penalties) {
                    const parts = [];

                    if (result.extraTime) {
                        const etHome = parseInt(result.homeScoreET || 0);
                        const etAway = parseInt(result.awayScoreET || 0);
                        parts.push(`${etHome} - ${etAway} AET`);
                    }

                    if (result.penalties) {
                        const pHome = parseInt(result.homePens || 0);
                        const pAway = parseInt(result.awayPens || 0);
                        parts.push(`${pHome} - ${pAway} pens`);
                    }

                    extraInfoHtml = `<div class="text-[10px] text-text-muted font-normal mt-1 text-center">(${parts.join(', ')})</div>`;
                }

                if (fixtureDetailUrl) {
                    scoreHtml = `
                        <div class="flex flex-col items-center">
                            <a href="${fixtureDetailUrl}" class="font-bold text-xl text-text-main bg-surface-hover px-3 py-1 rounded-sm leading-none hover:bg-primary hover:text-white transition-colors">
                                ${homeScore} - ${awayScore}
                            </a>
                            ${extraInfoHtml}
                        </div>
                    `;
                } else {
                    scoreHtml = `
                        <div class="flex flex-col items-center">
                            <div class="font-bold text-xl text-text-main bg-surface-hover px-3 py-1 rounded-sm leading-none">
                                ${homeScore} - ${awayScore}
                            </div>
                            ${extraInfoHtml}
                        </div>
                    `;
                }
            } else {
                if (fixtureDetailUrl) {
                    scoreHtml = `<a href="${fixtureDetailUrl}" class="text-base text-text-muted bg-transparent font-medium hover:text-primary transition-colors">${escapeHtml(time)}</a>`;
                } else {
                    scoreHtml = `<div class="text-base text-text-muted bg-transparent font-medium">${escapeHtml(time)}</div>`;
                }
            }

            const homeLink = homeId ? `<a href="<?= $basePath ?>/team/${homeSlug}" class="hover:text-primary transition-colors">${homeName}</a>` : homeName;
            const awayLink = awayId ? `<a href="<?= $basePath ?>/team/${awaySlug}" class="hover:text-primary transition-colors">${awayName}</a>` : awayName;

            return `
                <div class="flex flex-row items-center py-4 gap-4 hover:bg-surface-hover/50 transition-colors rounded-sm border-b border-border last:border-0 border-opacity-50">
                    <div class="flex-1 flex items-center justify-center gap-4 md:gap-8 w-full">
                        <div class="flex-1 flex items-center justify-end gap-3 font-semibold text-right">
                            ${homeLink}
                            <span class="inline-block w-4 h-4 rounded-full bg-current shadow-sm" style="color: ${homeColour}; background-color: ${homeColour}"></span>
                        </div>
                        ${scoreHtml}
                        <div class="flex-1 flex items-center justify-start gap-3 font-semibold text-left">
                            <span class="inline-block w-4 h-4 rounded-full bg-current shadow-sm" style="color: ${awayColour}; background-color: ${awayColour}"></span>
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