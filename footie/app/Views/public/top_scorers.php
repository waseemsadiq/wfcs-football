<?php
/**
 * Public Top Scorers Page - Leaderboards with AJAX filtering
 */
?>

<div class="w-full">
    <?php
    $title = 'Top Scorers';
    $subtitle = null;
    include __DIR__ . '/../partials/page_header.php';
    ?>

    <!-- League Filter -->
    <?php if (!empty($leagues)): ?>
        <div class="mb-8 max-w-md mx-auto">
            <div class="card p-6">
                <label for="league-filter" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                    Filter by League
                </label>
                <select id="league-filter"
                    class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    <option value="">All Competitions</option>
                    <?php foreach ($leagues as $league): ?>
                        <option value="<?= $league['id'] ?>" <?= ($selectedLeagueId == $league['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($league['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <!-- Loading State -->
    <div id="loading-state" class="hidden">
        <div class="card">
            <div class="text-center py-12" role="status" aria-live="polite">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4" aria-hidden="true"></div>
                <p class="text-text-muted">Loading top scorers...</p>
            </div>
        </div>
    </div>

    <!-- Leaderboard Content -->
    <div id="scorers-content">
        <div class="card overflow-hidden">
            <div class="overflow-x-auto" id="scorers-table">
                <?php include __DIR__ . '/partials/top_scorers_table.php'; ?>
            </div>
        </div>

        <!-- Legend for top 3 -->
        <div class="mt-6 flex justify-center gap-6 text-sm text-text-muted">
            <div class="flex items-center gap-2">
                <span class="font-bold text-yellow-400">1st</span>
                <span>Gold</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-400">2nd</span>
                <span>Silver</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold text-orange-400">3rd</span>
                <span>Bronze</span>
            </div>
        </div>
    </div>

    <!-- Error State -->
    <div id="error-state" class="hidden">
        <div class="card">
            <div class="text-center py-12 text-danger">
                <p id="error-message">Unable to load top scorers. Please try again.</p>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const leagueFilter = document.getElementById('league-filter');
        const loadingState = document.getElementById('loading-state');
        const scorersContent = document.getElementById('scorers-content');
        const errorState = document.getElementById('error-state');
        const scorersTable = document.getElementById('scorers-table');
        const errorMessage = document.getElementById('error-message');

        if (!leagueFilter) return;

        // Load top scorers data via AJAX
        async function loadTopScorers(leagueId) {
            // Show loading state
            scorersContent.classList.add('hidden');
            errorState.classList.add('hidden');
            loadingState.classList.remove('hidden');

            try {
                const url = leagueId
                    ? `<?= $basePath ?>/top-scorers/data?league_id=${leagueId}`
                    : `<?= $basePath ?>/top-scorers/data`;

                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error('Failed to load top scorers');
                }

                const data = await response.json();

                // Server-rendered HTML (sanitized with htmlspecialchars in partial)
                if (data.tableHtml) {
                    scorersTable.innerHTML = data.tableHtml;
                } else {
                    scorersTable.innerHTML = '<div class="text-center py-12 text-text-muted"><p>No goals recorded yet.</p></div>';
                }

                // Hide loading, show content
                loadingState.classList.add('hidden');
                scorersContent.classList.remove('hidden');

            } catch (error) {
                console.error('Error loading top scorers:', error);
                errorMessage.textContent = 'Unable to load top scorers. Please try again.';
                loadingState.classList.add('hidden');
                errorState.classList.remove('hidden');
            }
        }

        // Handle league filter change
        leagueFilter.addEventListener('change', function () {
            loadTopScorers(this.value);
        });

        // Initial load if league is pre-selected
        <?php if ($selectedLeagueId): ?>
        loadTopScorers('<?= $selectedLeagueId ?>');
        <?php endif; ?>
    })();
</script>
