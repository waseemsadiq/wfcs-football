<div class="">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold m-0 tracking-tight">Edit Cup</h1>
            <p class="text-text-muted mt-1 text-lg"><?= htmlspecialchars($cup['name']) ?></p>
        </div>
        <div class="flex gap-4">
            <a href="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/fixtures"
                class="btn btn-primary">Manage Fixtures</a>
            <a href="<?=$basePath?>/admin/cups" class="btn btn-secondary">Back to Cups</a>
        </div>
    </div>

    <div class="card mb-8">
        <form method="POST" action="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/update">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-text-muted mb-2">Cup Name <span
                        class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-input" required aria-required="true"
                    value="<?= htmlspecialchars($cup['name']) ?>" placeholder="Enter cup name">
            </div>

            <div class="pt-6 border-t border-border">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-text-main">Scheduling Defaults</h3>
                    <p class="text-sm text-text-muted">Used when regenerating the remaining bracket fixtures.</p>
                </div>
                <?php
                $required = false;
                $entity = $cup;
                include __DIR__ . '/../partials/scheduling_fields.php';
                ?>
            </div>

            <!-- Team Management Section -->
            <div class="pt-6 border-t border-border">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-text-main">Teams</h3>
                    <p class="text-sm text-text-muted">Add or remove teams from this cup.</p>
                </div>

                <div class="bg-warning/10 text-warning p-4 rounded-sm border border-warning/20 mb-6">
                    <p class="text-sm">
                        <strong>⚠️ Warning:</strong> Adding or removing teams will regenerate the bracket.
                        Matches with recorded results will be preserved.
                    </p>
                </div>

                <?php
                $label = 'Cup Teams';
                $required = false;
                $showCount = true;
                $showGrouped = true;  // NEW: Show current teams separate from available teams
                include __DIR__ . '/../partials/team_selector.php';
                ?>
            </div>

            <div class="flex items-center gap-4 pt-6 border-t border-border">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>"
                    class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <?php
    $entityType = 'cup';
    $deleteUrl = $basePath . '/admin/cups/' . htmlspecialchars($cup['slug'] ?? $cup['id']) . '/delete';
    $customMessage = 'Permanently delete this cup and all its fixture data.';
    include __DIR__ . '/../partials/danger_zone.php';
    ?>
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
            if (!confirm('You have changed the teams in this cup. This will regenerate the bracket for all unplayed matches. Continue?')) {
                e.preventDefault();
            }
        }
    });
});
</script>