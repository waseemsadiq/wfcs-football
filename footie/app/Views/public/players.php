<?php
/**
 * Public Players Page - Player directory with AJAX pagination
 */
?>

<div class="w-full">
    <?php
    $title = 'Players';
    $subtitle = null;
    include __DIR__ . '/../partials/page_header.php';
    ?>

    <!-- Team Filter -->
    <?php if (!empty($teams)): ?>
        <div class="mb-8 max-w-md mx-auto">
            <div class="card p-6">
                <label for="team-filter" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                    Filter by Team
                </label>
                <select id="team-filter"
                    class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                    onchange="filterByTeam(this.value)">
                    <option value="">All Teams</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['id'] ?>" <?= ($selectedTeamId == $team['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($team['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <div id="players-loader" class="hidden flex justify-center py-8" role="status" aria-live="polite">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        <span class="sr-only">Loading players...</span>
    </div>

    <div id="players-container">
        <?php include __DIR__ . '/partials/players_grid.php'; ?>
    </div>
</div>

<script>
    const BASE_PATH = '<?= $basePath ?>';
    let currentTeamId = '<?= $selectedTeamId ?? '' ?>';
    let currentPage = 1;

    // Filter functionality
    const teamFilter = document.getElementById('team-filter');
    const playersContainer = document.getElementById('players-container');
    const playersLoader = document.getElementById('players-loader');

    teamFilter?.addEventListener('change', function() {
        currentTeamId = this.value;
        currentPage = 1;
        loadPlayers();
    });

    function loadPlayers(page = 1) {
        currentPage = page;
        playersLoader.classList.remove('hidden');
        playersContainer.style.opacity = '0.5';

        let url = `${BASE_PATH}/ajax/players/list?`;
        const params = [];

        if (currentTeamId) {
            params.push(`team_id=${currentTeamId}`);
        }

        if (page > 1) {
            params.push(`page=${page}`);
        }

        url += params.join('&');

        fetch(url)
            .then(response => response.text())
            .then(html => {
                // Safe to use innerHTML here - content is from our own trusted server endpoint
                playersContainer.innerHTML = html;

                // Reinitialize pagination after loading new content
                initPagination();
            })
            .catch(error => {
                console.error('Error:', error);
                playersContainer.innerHTML = '<div class="text-error text-center py-8">Failed to load players.</div>';
            })
            .finally(() => {
                playersLoader.classList.add('hidden');
                playersContainer.style.opacity = '1';
            });
    }

    // Pagination functionality
    function initPagination() {
        // Handle pagination button clicks
        document.querySelectorAll('[data-pagination-prev]').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.disabled && currentPage > 1) {
                    loadPlayers(currentPage - 1);
                }
            });
        });

        document.querySelectorAll('[data-pagination-next]').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.disabled) {
                    loadPlayers(currentPage + 1);
                }
            });
        });

        document.querySelectorAll('[data-page]').forEach(btn => {
            btn.addEventListener('click', function() {
                const page = parseInt(this.dataset.page);
                if (page !== currentPage) {
                    loadPlayers(page);
                }
            });
        });
    }

    // Initialize on page load
    initPagination();

    function filterByTeam(teamId) {
        // Reset to page 1 when filtering
        currentTeamId = teamId;
        currentPage = 1;
        loadPlayers();
    }
</script>
