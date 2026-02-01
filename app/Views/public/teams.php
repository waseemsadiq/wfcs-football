<?php
/**
 * Public Teams Page
 */
?>

<div class="w-full">
    <?php
    $title = 'Teams';
    $subtitle = (isset($seasonName) && $seasonName) ? htmlspecialchars($seasonName) . ' Season' : null;
    include __DIR__ . '/../partials/page_header.php';
    ?>

    <?php if (empty($teams)): ?>
        <div class="card">
            <div class="text-center py-12 text-text-muted">
                <p>No teams available.</p>
            </div>
        </div>
    <?php else: ?>
        <!-- Team Selector -->
        <div class="mb-8 max-w-md mx-auto">
            <div class="card p-6">
                <label for="team-select" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                    Select Team
                </label>
                <select id="team-select"
                    class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= htmlspecialchars($team['slug']) ?>">
                            <?= htmlspecialchars($team['name']) ?>
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
                    <p class="text-text-muted">Loading team data...</p>
                </div>
            </div>
        </div>

        <!-- Content Container -->
        <div id="team-content" class="hidden">
            <!-- Team Header -->
            <div class="flex items-center gap-6 mb-12 bg-surface p-8 rounded-md shadow-md border border-border"
                id="team-header">
                <!-- Content injected via JS -->
            </div>

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
                    <p id="error-message">Unable to load team data. Please try again.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    (function () {
        const teamSelect = document.getElementById('team-select');
        const loadingState = document.getElementById('loading-state');
        const teamContent = document.getElementById('team-content');
        const errorState = document.getElementById('error-state');
        const teamHeader = document.getElementById('team-header');

        const recentResultsSection = document.getElementById('recent-results-section');
        const recentResultsContent = document.getElementById('recent-results-content');

        const upcomingFixturesSection = document.getElementById('upcoming-fixtures-section');
        const upcomingFixturesContent = document.getElementById('upcoming-fixtures-content');

        const errorMessage = document.getElementById('error-message');

        if (!teamSelect) return;

        // Load team data
        async function loadTeam(slug) {
            // Show loading state
            teamContent.classList.add('hidden');
            errorState.classList.add('hidden');
            loadingState.classList.remove('hidden');

            try {
                const response = await fetch(`<?= $basePath ?>/teams/${slug}/data`);

                if (!response.ok) {
                    throw new Error('Failed to load team data');
                }

                const data = await response.json();

                // Render Header
                renderHeader(data.team, data.competitions);

                // Render Recent Results
                if (data.recentResults && data.recentResults.length > 0) {
                    renderFixtures(data.recentResults, recentResultsContent);
                    recentResultsSection.classList.remove('hidden');
                } else {
                    recentResultsSection.classList.add('hidden');
                }

                // Render Upcoming Fixtures
                if (data.upcomingFixtures && data.upcomingFixtures.length > 0) {
                    renderFixtures(data.upcomingFixtures, upcomingFixturesContent);
                    upcomingFixturesSection.classList.remove('hidden');
                } else {
                    upcomingFixturesSection.classList.add('hidden');
                }

                // Hide loading, show content
                loadingState.classList.add('hidden');
                teamContent.classList.remove('hidden');

                // Save to localStorage
                localStorage.setItem('selectedTeamSlug', slug);

            } catch (error) {
                console.error('Error loading team:', error);
                loadingState.classList.add('hidden');
                errorState.classList.remove('hidden');
                errorMessage.textContent = error.message || 'Unable to load team data. Please try again.';
            }
        }

        function renderHeader(team, competitions) {
            let compsHtml = '';
            if (competitions && competitions.length > 0) {
                compsHtml = '<div class="flex flex-wrap gap-2 mt-3">';
                competitions.forEach(comp => {
                    const detailHtml = comp.detail ? `<span class="text-text-muted font-medium">(${escapeHtml(comp.detail)})</span>` : '';
                    compsHtml += `
                        <a href="<?= $basePath ?>${comp.url}"
                            class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-primary/10 text-primary hover:bg-primary/20 transition-colors border border-primary/20 uppercase tracking-wide gap-1">
                            ${escapeHtml(comp.name)}
                            ${detailHtml}
                        </a>
                    `;
                });
                compsHtml += '</div>';
            }

            teamHeader.innerHTML = `
                <div class="w-16 h-16 rounded-md shadow-lg ring-2 ring-border flex-shrink-0"
                    style="background-color: ${escapeHtml(team.colour || '#333333')}">
                </div>
                <div>
                    <h1 class="text-4xl font-extrabold m-0 text-text-main">
                        ${escapeHtml(team.name)}
                    </h1>
                    ${compsHtml}
                </div>
            `;
        }

        function renderFixtures(fixtures, container) {
            let html = '';

            // Group by date, similar to Cups/Leagues for consistency
            // Or just list them? The single team page just lists them.
            // But separating them into "Recent" and "Upcoming" makes sense to list chronologically (desc for recent, asc for upcoming).
            // The API returns them sorted that way.

            // Let's use the list style from team.php but adapted for AJAX

            fixtures.forEach(fixture => {
                html += renderFixture(fixture);
            });

            container.innerHTML = html;
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

            let dateDisplay = '';
            if (fixture.date) {
                const d = new Date(fixture.date);
                dateDisplay = d.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
            }

            let compDisplay = escapeHtml(fixture.competitionName || '');
            if (fixture.roundName) {
                compDisplay += ' - ' + escapeHtml(fixture.roundName);
            }

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

                scoreHtml = `
                    <div class="flex flex-col items-center">
                        <div class="font-bold text-xl text-primary bg-surface-hover px-3 py-1 rounded-sm">
                            ${homeScore} - ${awayScore}
                        </div>
                        ${extraInfoHtml}
                    </div>
                `;
            } else {
                scoreHtml = `<div class="text-base text-text-muted bg-transparent font-medium">${escapeHtml(time)}</div>`;
            }

            const homeLink = homeId ? `<a href="<?= $basePath ?>/team/${homeSlug}" class="hover:text-primary transition-colors">${homeName}</a>` : homeName;
            const awayLink = awayId ? `<a href="<?= $basePath ?>/team/${awaySlug}" class="hover:text-primary transition-colors">${awayName}</a>` : awayName;

            return `
                <li class="flex flex-col items-center py-4 border-b border-border last:border-b-0 gap-1 hover:bg-surface-hover/50 transition-colors px-4 -mx-4 rounded-sm">
                    <div class="text-xs text-text-muted font-bold uppercase tracking-wider mb-1">${dateDisplay}</div>
                    <div class="flex items-center justify-center gap-4 md:gap-8 w-full">
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
                    <div class="text-xs text-text-muted font-medium text-center">${compDisplay}</div>
                </li>
            `;
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Event listener for team selection
        teamSelect.addEventListener('change', function () {
            loadTeam(this.value);
        });

        // On page load: check localStorage or select first
        const savedSlug = localStorage.getItem('selectedTeamSlug');
        if (savedSlug) {
            // Check if saved team exists in options
            const option = Array.from(teamSelect.options).find(opt => opt.value === savedSlug);
            if (option) {
                teamSelect.value = savedSlug;
            }
        }

        // Load the selected team
        loadTeam(teamSelect.value);
    })();
</script>