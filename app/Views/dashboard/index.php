<?php $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); ?>
<div class="text-center mb-12">
    <h1
        class="text-4xl font-extrabold tracking-tight mb-2 text-text-main">
        Welcome back</h1>
</div>

<?php if ($activeSeason): ?>
    <!-- Upcoming Fixtures -->
    <div class="card mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-xl font-bold m-0">Upcoming Fixtures</h2>
            <select id="competition-select"
                class="bg-surface border border-border rounded px-3 py-2 text-text-main focus:outline-none focus:border-primary min-w-[200px]">
                <option value="" disabled selected>Select Competition</option>
                <?php if (!empty($leagues)): ?>
                    <optgroup label="Leagues">
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?= $league['id'] ?>" data-type="league"
                                data-slug="<?= $league['slug'] ?? $league['id'] ?>">
                                <?= htmlspecialchars($league['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
                <?php if (!empty($cups)): ?>
                    <optgroup label="Cups">
                        <?php foreach ($cups as $cup): ?>
                            <option value="<?= $cup['id'] ?>" data-type="cup" data-slug="<?= $cup['slug'] ?? $cup['id'] ?>">
                                <?= htmlspecialchars($cup['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
            </select>
        </div>

        <div id="fixtures-loader" class="hidden flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div id="fixtures-container" class="space-y-4 opacity-100 transition-opacity duration-300">
            <div class="text-center text-text-muted py-8 bg-surface-hover/20 rounded border border-border border-dashed">
                Select a competition to view upcoming matches.
            </div>
        </div>
    </div>

    <!-- Active Season Summary -->
    <div class="card mb-8">
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 border-b border-border pb-4 gap-4">
            <div>
                <h2 class="text-2xl font-bold mb-0"><?= htmlspecialchars($activeSeason['name']) ?></h2>
                <span class="text-text-muted">Active Season</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?= $basePath ?>/admin/teams/create" class="btn btn-secondary btn-sm">+ Add Team</a>
                <a href="<?= $basePath ?>/admin/leagues/create" class="btn btn-secondary btn-sm">+ Create League</a>
                <a href="<?= $basePath ?>/admin/cups/create" class="btn btn-secondary btn-sm">+ Create Cup</a>
                <a href="<?= $basePath ?>/admin/seasons" class="btn btn-secondary btn-sm">Manage Seasons</a>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="text-center">
                <h3 class="text-sm text-text-muted mb-2 uppercase tracking-wide font-semibold">Teams</h3>
                <p class="text-4xl font-extrabold text-primary m-0"><?= $teamCount ?></p>
                <a href="<?= $basePath ?>/admin/teams"
                    class="text-xs text-text-muted hover:text-primary transition-colors">View all</a>
            </div>

            <div class="text-center">
                <h3 class="text-sm text-text-muted mb-2 uppercase tracking-wide font-semibold">Leagues</h3>
                <p class="text-4xl font-extrabold text-primary m-0"><?= count($leagues) ?></p>
                <a href="<?= $basePath ?>/admin/leagues"
                    class="text-xs text-text-muted hover:text-primary transition-colors">View
                    all</a>
            </div>

            <div class="text-center">
                <h3 class="text-sm text-text-muted mb-2 uppercase tracking-wide font-semibold">Cups</h3>
                <p class="text-4xl font-extrabold text-primary m-0"><?= count($cups) ?></p>
                <a href="<?= $basePath ?>/admin/cups"
                    class="text-xs text-text-muted hover:text-primary transition-colors">View all</a>
            </div>
        </div>

        <?php if (!empty($leagues)): ?>
            <div class="mb-8">
                <h3 class="mb-4 text-lg font-bold">League Competitions</h3>
                <ul class="border border-border rounded-sm overflow-hidden bg-background">
                    <?php foreach ($leagues as $league): ?>
                        <li
                            class="flex items-center gap-4 px-5 py-4 bg-background border-b border-border last:border-b-0 hover:bg-surface-hover transition-colors">
                            <a href="<?= $basePath ?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
                                class="flex-1 font-semibold text-text-main hover:text-primary transition-colors">
                                <?= htmlspecialchars($league['name']) ?>
                            </a>
                            <span class="text-text-muted text-sm"><?= count($league['teamIds'] ?? []) ?> teams</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($cups)): ?>
            <div class="mt-8 mb-4">
                <h3 class="mb-4 text-lg font-bold">Cup Competitions</h3>
                <ul class="border border-border rounded-sm overflow-hidden bg-background">
                    <?php foreach ($cups as $cup): ?>
                        <li
                            class="flex items-center gap-4 px-5 py-4 bg-background border-b border-border last:border-b-0 hover:bg-surface-hover transition-colors">
                            <a href="<?= $basePath ?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>"
                                class="flex-1 font-semibold text-text-main hover:text-primary transition-colors">
                                <?= htmlspecialchars($cup['name']) ?>
                            </a>
                            <span class="text-text-muted text-sm"><?= count($cup['teamIds'] ?? []) ?> teams</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="card text-center p-12">
        <h2 class="text-error mb-4">No Active Season</h2>
        <p class="text-text-muted mb-8">Create a season and set it as active to see your competitions here.</p>
        <a href="<?= $basePath ?>/admin/seasons/create" class="btn btn-primary">Create Season</a>
    </div>
<?php endif; ?>

<script>
    const CSRF_TOKEN = '<?= htmlspecialchars($csrfToken ?? '') ?>';
    const BASE_PATH = '<?= $basePath ?>';

    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('competition-select');
        const container = document.getElementById('fixtures-container');
        const loader = document.getElementById('fixtures-loader');

        // Auto-select first option if available
        const firstOption = select.querySelector('option:not([disabled])');
        if (firstOption) {
            select.value = firstOption.value;
            loadFixtures(firstOption);
        }

        select.addEventListener('change', function () {
            const option = this.options[this.selectedIndex];
            loadFixtures(option);
        });

        function loadFixtures(option) {
            const type = option.dataset.type;
            const slug = option.dataset.slug;
            const id = option.value;

            // Animate out
            container.style.opacity = '0.5';
            loader.classList.remove('hidden');

            fetch(`${BASE_PATH}/admin/dashboard/upcoming-fixtures?type=${type}&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    renderFixtures(data.fixtures || [], type, slug);
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = '<div class="text-error text-center py-4">Failed to load fixtures.</div>';
                })
                .finally(() => {
                    loader.classList.add('hidden');
                    container.style.opacity = '1';
                });
        }

        function renderFixtures(fixtures, type, slug) {
            if (fixtures.length === 0) {
                container.innerHTML = '<div class="text-center text-text-muted py-8">No upcoming fixtures scheduled.</div>';
                return;
            }

            const today = new Date().toISOString().split('T')[0];

            // Group fixtures by date
            const fixturesByDate = {};
            fixtures.forEach(fixture => {
                const date = fixture.date;
                if (!fixturesByDate[date]) {
                    fixturesByDate[date] = [];
                }
                fixturesByDate[date].push(fixture);
            });

            let html = '<div class="grid gap-3">';

            // Render each date group
            Object.keys(fixturesByDate).sort().forEach(date => {
                const dateObj = new Date(date);
                const dateStr = dateObj.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long' });

                html += `<h3 class="text-sm font-bold text-text-muted uppercase tracking-wider mb-2 mt-4 first:mt-0">${dateStr}</h3>`;

                fixturesByDate[date].forEach(fixture => {
                    const showInputs = fixture.date <= today;
                    const homeName = fixture.homeTeam ? fixture.homeTeam.name : 'TBD';
                    const awayName = fixture.awayTeam ? fixture.awayTeam.name : 'TBD';
                    const homeColor = fixture.homeTeam ? fixture.homeTeam.colour : '#333';
                    const awayColor = fixture.awayTeam ? fixture.awayTeam.colour : '#333';

                    html += `
                        <div class="flex flex-row items-center gap-4 bg-surface-hover/30 border border-border rounded-lg p-3 hover:border-primary/30 transition-colors">
                            <div class="flex-1 flex items-center justify-end gap-3 text-right">
                                <span class="font-semibold text-sm md:text-base">${homeName}</span>
                                <span class="w-3 h-3 rounded-sm shadow-sm" style="background-color: ${homeColor}"></span>
                            </div>
                            
                            <div class="flex flex-col items-center justify-center min-w-[120px]">
                                ${showInputs ? renderScoreInputs(fixture, type, slug) : renderTime(fixture)}
                            </div>

                            <div class="flex-1 flex items-center justify-start gap-3 text-left">
                                <span class="w-3 h-3 rounded-sm shadow-sm" style="background-color: ${awayColor}"></span>
                                <span class="font-semibold text-sm md:text-base">${awayName}</span>
                            </div>
                        </div>
                    `;
                });
            });

            html += '</div>';
            container.innerHTML = html;
        }

        function renderTime(fixture) {
            return `
                <div class="bg-surface border border-border px-3 py-1 rounded text-sm font-mono font-bold text-text-main">
                    ${fixture.time}
                </div>
            `;
        }

        function renderScoreInputs(fixture, type, slug) {
            return `
                <form onsubmit="saveScore(event, '${type}', '${slug}', '${fixture.id}')" class="flex items-center gap-2">
                    <input type="number" name="homeScore" min="0" max="99" class="w-10 text-center p-1 rounded bg-surface border border-border focus:border-primary focus:outline-none text-sm font-bold" placeholder="-">
                    <span class="text-text-muted font-bold">:</span>
                    <input type="number" name="awayScore" min="0" max="99" class="w-10 text-center p-1 rounded bg-surface border border-border focus:border-primary focus:outline-none text-sm font-bold" placeholder="-">
                    <button type="submit" class="p-1 rounded bg-primary text-white hover:bg-primary-hover shadow-sm transition-colors" title="Save Result">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </form>
            `;
        }
    });

    function saveScore(event, type, slug, fixtureId) {
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

        fetch(`${BASE_PATH}/admin/${type}s/${slug}/fixtures`, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                // Some endpoints might return redirect, catch that.
                if (response.redirected) return { success: true };
                return response.text().then(text => {
                    try { return JSON.parse(text); } catch { return { success: true }; }
                });
            })
            .then(data => {
                // Show success state then reload fixtures
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
                btn.classList.add('bg-blue-600');
                setTimeout(() => {
                    const select = document.getElementById('competition-select');
                    const option = select.options[select.selectedIndex];
                    // Trigger change event to reload
                    select.dispatchEvent(new Event('change'));
                }, 1000);
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalContent;
                alert('Failed to save score');
            });
    }
</script>