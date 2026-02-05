<div class="text-center mb-12">
    <h1
        class="text-4xl font-extrabold tracking-tight mb-2 text-text-main">
        Edit League</h1>
    <h2 class="text-2xl font-bold mb-6 text-text-muted"><?= htmlspecialchars($league['name']) ?></h2>
</div>

<div class="card mb-8">
    <form method="POST" action="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/update">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <div class="mb-8">
            <label for="name" class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">League
                Name *</label>
            <input type="text" id="name" name="name" required aria-required="true" value="<?= htmlspecialchars($league['name']) ?>"
                placeholder="Enter league name" class="form-input">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div>
                <label for="startDate"
                    class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">First Fixture
                    Date</label>
                <input type="date" id="startDate" name="startDate"
                    value="<?= htmlspecialchars($league['startDate'] ?? '') ?>" class="form-input">
            </div>

            <div>
                <label for="frequency"
                    class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Match
                    Frequency</label>
                <select id="frequency" name="frequency" class="form-input">
                    <option value="weekly" <?= ($league['frequency'] ?? 'weekly') === 'weekly' ? 'selected' : '' ?>>Weekly
                    </option>
                    <option value="fortnightly" <?= ($league['frequency'] ?? '') === 'fortnightly' ? 'selected' : '' ?>>
                        Fortnightly</option>
                    <option value="monthly" <?= ($league['frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly
                    </option>
                </select>
            </div>

            <div>
                <label for="matchTime"
                    class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Typical Match
                    Time</label>
                <input type="time" id="matchTime" name="matchTime"
                    value="<?= htmlspecialchars(substr($league['matchTime'] ?? '15:00', 0, 5)) ?>" class="form-input">
            </div>
        </div>

        <!-- Team Management Section -->
        <div class="pt-6 border-t border-border">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-text-main">Teams</h3>
                <p class="text-sm text-text-muted">Add or remove teams from this league.</p>
            </div>

            <div class="bg-warning/10 text-warning p-4 rounded-sm border border-warning/20 mb-6">
                <p class="text-sm">
                    <strong>⚠️ Warning:</strong> Adding or removing teams will regenerate all unplayed fixtures.
                    Fixtures with recorded results will be preserved.
                </p>
            </div>

            <?php
            $label = 'League Teams';
            $required = false;
            $showCount = true;
            $showGrouped = true;  // NEW: Show current teams separate from available teams
            include __DIR__ . '/../partials/team_selector.php';
            ?>
        </div>

        <div class="flex gap-4 mt-8 border-t border-border pt-8">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>"
                class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- JavaScript: Track team changes and confirm on save -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const checkboxes = document.querySelectorAll('.team-checkbox');
    const originalTeams = new Set([...checkboxes].filter(cb => cb.checked).map(cb => cb.value));

    form.addEventListener('submit', function(e) {
        const currentTeams = new Set([...checkboxes].filter(cb => cb.checked).map(cb => cb.value));
        const teamsChanged = originalTeams.size !== currentTeams.size ||
            [...originalTeams].some(id => !currentTeams.has(id));

        if (teamsChanged) {
            if (!confirm('You have changed the teams in this league. This will regenerate all unplayed fixtures. Continue?')) {
                e.preventDefault();
            }
        }
    });
});
</script>

<?php
$entityType = 'league';
$deleteUrl = $basePath . '/admin/leagues/' . htmlspecialchars($league['slug'] ?? $league['id']) . '/delete';
$customMessage = 'Permanently delete this league and all its fixture data.';
include __DIR__ . '/../partials/danger_zone.php';
?>