<?php
/**
 * Render a fixture row for leagues page.
 */
if (!function_exists('renderLeaguesFixture')) {
    function renderLeaguesFixture(array $fixture): string
    {
        $homeTeam = $fixture['homeTeam'] ?? null;
        $awayTeam = $fixture['awayTeam'] ?? null;
        $result = $fixture['result'] ?? null;

        $date = '';
        if (!empty($fixture['date'])) {
            $dateObj = new DateTime($fixture['date']);
            $date = $dateObj->format('D j M');
        }

        $time = $fixture['time'] ?? '15:00';

        $homeColour = htmlspecialchars($homeTeam['colour'] ?? '#333333');
        $awayColour = htmlspecialchars($awayTeam['colour'] ?? '#333333');
        $homeName = htmlspecialchars($homeTeam['name'] ?? 'TBD');
        $awayName = htmlspecialchars($awayTeam['name'] ?? 'TBD');
        $homeId = htmlspecialchars($homeTeam['id'] ?? '');
        $awayId = htmlspecialchars($awayTeam['id'] ?? '');

        $scoreHtml = '';
        if ($result) {
            $homeScore = (int) ($result['homeScore'] ?? 0);
            $awayScore = (int) ($result['awayScore'] ?? 0);
            $scoreHtml = '<div class="font-bold text-xl text-primary bg-surface-hover px-3 py-1 rounded-sm">' . $homeScore . ' - ' . $awayScore . '</div>';
        } else {
            $scoreHtml = '<div class="text-base text-text-muted bg-transparent font-medium">' . htmlspecialchars($time) . '</div>';
        }

        $homeSlug = htmlspecialchars($homeTeam['slug'] ?? $homeId);
        $awaySlug = htmlspecialchars($awayTeam['slug'] ?? $awayId);

        $homeLink = $homeId ? "<a href=\"/team/{$homeSlug}\" class=\"hover:text-primary transition-colors\">{$homeName}</a>" : $homeName;
        $awayLink = $awayId ? "<a href=\"/team/{$awaySlug}\" class=\"hover:text-primary transition-colors\">{$awayName}</a>" : $awayName;

        return <<<HTML
        <li class="flex flex-row items-center py-4 border-b border-border last:border-b-0 gap-4 hover:bg-surface-hover/50 transition-colors px-4 -mx-4 rounded-sm">
            <div class="min-w-[80px] text-center text-text-muted text-sm font-bold">
                <div>{$date}</div>
            </div>
            <div class="flex-1 flex items-center justify-center gap-4 md:gap-8 w-full">
                <div class="flex-1 flex items-center justify-end gap-3 font-semibold text-right">
                    {$homeLink}
                    <span class="inline-block w-4 h-4 rounded bg-current shadow-sm" style="color: {$homeColour}; background-color: {$homeColour}"></span>
                </div>
                {$scoreHtml}
                <div class="flex-1 flex items-center justify-start gap-3 font-semibold text-left">
                    <span class="inline-block w-4 h-4 rounded bg-current shadow-sm" style="color: {$awayColour}; background-color: {$awayColour}"></span>
                    {$awayLink}
                </div>
            </div>
        </li>
HTML;
    }
}
?>

<div class="w-full">
    <div class="mb-8 text-center">
        <h1
            class="text-4xl font-extrabold m-0 text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400 mb-2">
            Leagues</h1>
        <?php if (isset($seasonName) && $seasonName): ?>
            <p class="text-text-muted text-lg font-medium mb-4"><?= htmlspecialchars($seasonName) ?> Season</p>
        <?php endif; ?>
    </div>

    <?php if (empty($leagues)): ?>
        <div class="card">
            <div class="text-center py-12 text-text-muted">
                <p>No leagues available for this season</p>
            </div>
        </div>
    <?php else: ?>
        <!-- League Selector -->
        <div class="mb-8 max-w-md mx-auto">
            <div class="card p-6">
                <label for="league-select" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                    Select League
                </label>
                <select id="league-select"
                    class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    <?php foreach ($leagues as $league): ?>
                        <option value="<?= htmlspecialchars($league['slug']) ?>">
                            <?= htmlspecialchars($league['name']) ?>
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
                    <p class="text-text-muted">Loading league data...</p>
                </div>
            </div>
        </div>

        <!-- Content Container -->
        <div id="league-content">
            <!-- Standings -->
            <section class="mb-16" id="standings-section">
                <div class="flex items-center gap-4 mb-6">
                    <h2 class="text-2xl font-bold">Standings</h2>
                    <div class="h-px bg-border flex-1"></div>
                </div>
                <div class="card p-0 overflow-hidden">
                    <div id="standings-content" class="p-0"></div>
                </div>
            </section>

            <!-- Recent Results -->
            <section class="mb-16" id="recent-results-section">
                <div class="flex items-center gap-4 mb-6">
                    <h2 class="text-2xl font-bold">Recent Results</h2>
                    <div class="h-px bg-border flex-1"></div>
                </div>
                <div class="card">
                    <ul id="recent-results-content" class="divide-y divide-border"></ul>
                </div>
            </section>

            <!-- Upcoming Fixtures -->
            <section class="mb-16" id="upcoming-fixtures-section">
                <div class="flex items-center gap-4 mb-6">
                    <h2 class="text-2xl font-bold">Upcoming Fixtures</h2>
                    <div class="h-px bg-border flex-1"></div>
                </div>
                <div class="card">
                    <ul id="upcoming-fixtures-content" class="divide-y divide-border"></ul>
                </div>
            </section>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden">
            <div class="card">
                <div class="text-center py-12 text-danger">
                    <p id="error-message">Unable to load league data. Please try again.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    (function () {
        const leagueSelect = document.getElementById('league-select');
        const loadingState = document.getElementById('loading-state');
        const leagueContent = document.getElementById('league-content');
        const errorState = document.getElementById('error-state');

        const standingsSection = document.getElementById('standings-section');
        const recentResultsSection = document.getElementById('recent-results-section');
        const upcomingFixturesSection = document.getElementById('upcoming-fixtures-section');

        const standingsContent = document.getElementById('standings-content');
        const recentResultsContent = document.getElementById('recent-results-content');
        const upcomingFixturesContent = document.getElementById('upcoming-fixtures-content');
        const errorMessage = document.getElementById('error-message');

        if (!leagueSelect) return;

        // Load league data
        async function loadLeague(slug) {
            // Show loading state
            leagueContent.classList.add('hidden');
            errorState.classList.add('hidden');
            loadingState.classList.remove('hidden');

            try {
                const response = await fetch(`<?= $basePath ?>/leagues/${slug}/data`);

                if (!response.ok) {
                    throw new Error('Failed to load league data');
                }

                const data = await response.json();

                // Render standings
                renderStandings(data.standings);

                // Render recent results
                if (data.recentResults && data.recentResults.length > 0) {
                    renderFixtures(data.recentResults, recentResultsContent, true);
                    recentResultsSection.classList.remove('hidden');
                } else {
                    recentResultsSection.classList.add('hidden');
                }

                // Render upcoming fixtures
                if (data.upcomingFixtures && data.upcomingFixtures.length > 0) {
                    renderFixtures(data.upcomingFixtures, upcomingFixturesContent, false);
                    upcomingFixturesSection.classList.remove('hidden');
                } else {
                    upcomingFixturesSection.classList.add('hidden');
                }

                // Hide loading, show content
                loadingState.classList.add('hidden');
                leagueContent.classList.remove('hidden');

                // Save to localStorage
                localStorage.setItem('selectedLeagueSlug', slug);

            } catch (error) {
                console.error('Error loading league:', error);
                loadingState.classList.add('hidden');
                errorState.classList.remove('hidden');
                errorMessage.textContent = error.message || 'Unable to load league data. Please try again.';
            }
        }

        // Render standings table (NOTE: innerHTML used with properly escaped data)
        function renderStandings(standings) {
            if (!standings || standings.length === 0) {
                standingsContent.innerHTML = '<div class="text-center py-12 text-text-muted"><p>No standings available</p></div>';
                return;
            }

            let html = '<div class="overflow-x-auto"><table class="w-full border-collapse">';

            // Header
            html += `
                <thead>
                    <tr>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12">Pos</th>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-left">Team</th>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12" title="Played">P</th>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden sm:table-cell" title="Won">W</th>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden sm:table-cell" title="Drawn">D</th>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden sm:table-cell" title="Lost">L</th>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden md:table-cell" title="Goals For">GF</th>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12 hidden md:table-cell" title="Goals Against">GA</th>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-12" title="Goal Difference">GD</th>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-center w-16">Pts</th>
                        <th class="uppercase text-xs font-bold text-text-muted tracking-wider p-4 border-b border-border text-left hidden md:table-cell">Last 5</th>
                    </tr>
                </thead>
            `;

            // Body
            html += '<tbody>';
            standings.forEach((row, index) => {
                const pos = index + 1;
                const gdClass = row.goalDifference > 0 ? 'text-primary' : (row.goalDifference < 0 ? 'text-danger' : 'text-text-muted');
                const gdDisplay = row.goalDifference > 0 ? '+' + row.goalDifference : row.goalDifference;

                // Form Guide
                const formHtml = (row.form || []).map(r => {
                    let bg, lbl;
                    if (r === 'W') { bg = '#008744'; lbl = 'W'; }
                    else if (r === 'L') { bg = '#D61A21'; lbl = 'L'; }
                    else { bg = '#666666'; lbl = 'D'; }
                    return `<span class="inline-flex items-center justify-center w-8 h-8 rounded-sm text-white text-xs font-bold shadow-sm transform transition-transform hover:scale-110" style="background-color: ${bg}" title="${lbl}">${lbl}</span>`;
                }).join('');

                html += `
                    <tr class="border-b border-border hover:bg-surface-hover/50 transition-colors last:border-0">
                        <td class="p-4 text-center font-medium text-text-muted">${pos}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <span class="inline-block w-3 h-3 rounded-full shadow-sm flex-shrink-0" style="background-color: ${escapeHtml(row.teamColour)}"></span>
                                <span class="font-semibold text-text-main">
                                    <a href="<?= $basePath ?>/team/${escapeHtml(row.teamSlug || row.teamId)}" class="hover:text-primary transition-colors">
                                        ${escapeHtml(row.teamName)}
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td class="p-4 text-center text-text-muted">${row.played}</td>
                        <td class="p-4 text-center text-text-muted hidden sm:table-cell">${row.won}</td>
                        <td class="p-4 text-center text-text-muted hidden sm:table-cell">${row.drawn}</td>
                        <td class="p-4 text-center text-text-muted hidden sm:table-cell">${row.lost}</td>
                        <td class="p-4 text-center text-text-muted hidden md:table-cell">${row.goalsFor}</td>
                        <td class="p-4 text-center text-text-muted hidden md:table-cell">${row.goalsAgainst}</td>
                        <td class="p-4 text-center font-medium ${gdClass}">${gdDisplay}</td>
                        <td class="p-4 text-center font-bold text-lg text-white">${row.points}</td>
                        <td class="p-4 hidden md:table-cell">
                            <div class="flex items-center justify-start gap-1">
                                ${formHtml}
                            </div>
                        </td>
                    </tr>
                `;
            });
            html += '</tbody></table></div>';

            standingsContent.innerHTML = html;
        }

        // Render fixtures (NOTE: innerHTML used with properly escaped data)
        function renderFixtures(fixtures, container, showResult) {
            if (!fixtures || fixtures.length === 0) {
                container.innerHTML = '<li class="text-center py-12 text-text-muted"><p>No fixtures</p></li>';
                return;
            }

            let html = '<div class="flex flex-col">';

            // Re-implement with explicit grouping
            const groups = {};
            const dates = []; // to preserve order

            fixtures.forEach(f => {
                const d = f.date || 'TBD';
                if (!groups[d]) {
                    groups[d] = [];
                    dates.push(d);
                }
                groups[d].push(f);
            });

            dates.forEach(date => {
                let dateDisplay = 'TBD';
                if (date !== 'TBD') {
                    const dateObj = new Date(date + 'T00:00:00');
                    dateDisplay = dateObj.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
                }

                html += `
                    <div class="bg-surface/50 border-b border-border py-2 text-center sticky top-0 z-10">
                        <span class="text-xs font-bold text-text-muted uppercase tracking-wider">
                            ${dateDisplay}
                        </span>
                    </div>
                    <div class="divide-y divide-border border-b border-border last:border-0 px-4">
                `;

                groups[date].forEach(fixture => {
                    html += renderFixture(fixture, showResult);
                });

                html += '</div>';
            });

            html += '</div>';
            container.innerHTML = html;
        }

        // Render single fixture
        function renderFixture(fixture, showResult) {
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
            if (showResult && result) {
                const homeScore = parseInt(result.homeScore || 0);
                const awayScore = parseInt(result.awayScore || 0);
                scoreHtml = `<div class="font-bold text-xl text-primary bg-surface-hover px-3 py-1 rounded-sm">${homeScore} - ${awayScore}</div>`;
            } else {
                scoreHtml = `<div class="text-base text-text-muted bg-transparent font-medium">${escapeHtml(time)}</div>`;
            }

            const homeLink = homeId ? `<a href="<?= $basePath ?>/team/${homeSlug}" class="hover:text-primary transition-colors">${homeName}</a>` : homeName;
            const awayLink = awayId ? `<a href="<?= $basePath ?>/team/${awaySlug}" class="hover:text-primary transition-colors">${awayName}</a>` : awayLink;

            return `
                <div class="flex flex-row items-center py-4 gap-4 hover:bg-surface-hover/50 transition-colors rounded-sm">
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

        // Event listener for league selection
        leagueSelect.addEventListener('change', function () {
            loadLeague(this.value);
        });

        // On page load: check localStorage or select first
        const savedSlug = localStorage.getItem('selectedLeagueSlug');
        if (savedSlug) {
            // Check if saved league exists in options
            const option = Array.from(leagueSelect.options).find(opt => opt.value === savedSlug);
            if (option) {
                leagueSelect.value = savedSlug;
            }
        }

        // Load the selected league
        loadLeague(leagueSelect.value);
    })();
</script>