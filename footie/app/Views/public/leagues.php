<?php
/**
 * Render a fixture row for leagues page.
 */
?>

<div class="w-full">
    <?php
    $title = 'Leagues';
    $subtitle = (isset($seasonName) && $seasonName) ? htmlspecialchars($seasonName) . ' Season' : null;
    include __DIR__ . '/../partials/page_header.php';
    ?>

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
                <div class="text-center py-12" role="status" aria-live="polite">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"
                        aria-hidden="true"></div>
                    <p class="text-text-muted">Loading league data...</p>
                </div>
            </div>
        </div>

        <!-- Content Container -->
        <div id="league-content">
            <!-- Standings -->
            <section class="mb-16" id="standings-section">
                <div class="card p-0 overflow-hidden">
                    <?php
                    $title = 'Standings';
                    include __DIR__ . '/../partials/card_header.php';
                    ?>
                    <div id="standings-content" class="p-0"></div>
                </div>
            </section>

            <!-- Recent Results -->
            <section class="mb-16" id="recent-results-section">
                <div class="card p-0 overflow-hidden">
                    <?php
                    $title = 'Recent Results';
                    include __DIR__ . '/../partials/card_header.php';
                    ?>
                    <ul id="recent-results-content" class="divide-y divide-border"></ul>
                </div>
            </section>

            <!-- Upcoming Fixtures -->
            <section class="mb-16" id="upcoming-fixtures-section">
                <div class="card p-0 overflow-hidden">
                    <?php
                    $title = 'Upcoming Fixtures';
                    include __DIR__ . '/../partials/card_header.php';
                    ?>
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

                // Render standings (server-rendered HTML from shared partial)
                if (data.standingsHtml) {
                    standingsContent.innerHTML = data.standingsHtml;
                } else {
                    standingsContent.innerHTML = '<div class="text-center py-12 text-text-muted"><p>No standings available</p></div>';
                }

                // Render recent results
                if (data.recentResultsHtml) {
                    recentResultsContent.innerHTML = data.recentResultsHtml;
                    recentResultsSection.classList.remove('hidden');
                } else {
                    recentResultsSection.classList.add('hidden');
                }

                // Render upcoming fixtures
                if (data.upcomingFixturesHtml) {
                    upcomingFixturesContent.innerHTML = data.upcomingFixturesHtml;
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