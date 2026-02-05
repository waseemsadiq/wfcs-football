<?php
/**
 * Team Selector Grid
 *
 * Checkbox grid for selecting teams.
 *
 * @var array $teams - Array of team records
 * @var array $selectedTeamIds - Array of selected team IDs (optional)
 * @var string|null $label - Custom label text (optional)
 * @var string|null $description - Description text below label (optional)
 * @var bool $required - Whether selection is required (default: true)
 * @var bool $showCount - Whether to show team count (default: false)
 * @var bool $showGrouped - Whether to show grouped view: current vs available (default: false)
 */

$selectedTeamIds = $selectedTeamIds ?? [];
$label = $label ?? 'Select Teams';
$required = $required ?? true;
$showCount = $showCount ?? false;
$showGrouped = $showGrouped ?? false;
$requiredIndicator = $required ? ' *' : '';
$minTeams = $required ? ' (minimum 2)' : '';

// Separate teams into current (selected) and available (not selected) if grouped
$currentTeams = [];
$availableTeams = [];
if ($showGrouped && !empty($selectedTeamIds)) {
    foreach ($teams as $team) {
        if (in_array($team['id'], $selectedTeamIds)) {
            $currentTeams[] = $team;
        } else {
            $availableTeams[] = $team;
        }
    }
} else {
    // Use flat list when not grouped
    $availableTeams = $teams;
}
?>

<div class="mb-8">
    <div class="flex justify-between items-center mb-2">
        <label class="block font-semibold text-text-muted text-sm uppercase tracking-wide">
            <?= htmlspecialchars($label) ?><?= $requiredIndicator ?><?= $minTeams ?>
        </label>
        <?php if ($showCount): ?>
            <div class="text-sm text-text-muted">
                Teams selected: <span id="team-count" class="font-bold text-primary">
                    <?= count($selectedTeamIds) ?>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($description)): ?>
        <p class="text-sm text-text-muted mb-4"><?= htmlspecialchars($description) ?></p>
    <?php endif; ?>

    <?php if ($showGrouped && !empty($currentTeams)): ?>
        <!-- Current Teams Section -->
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-text-muted uppercase tracking-wide mb-3">
                Current Teams (<?= count($currentTeams) ?>)
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($currentTeams as $team): ?>
                    <label class="flex items-center gap-3 p-3 bg-background rounded-sm cursor-pointer border border-primary/30 hover:border-primary/50 transition-colors">
                        <input type="checkbox" name="teamIds[]" value="<?= htmlspecialchars($team['id']) ?>"
                               class="accent-primary w-5 h-5 team-checkbox"
                               checked>
                        <span class="inline-block w-3 h-3 rounded-full"
                              style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>;"></span>
                        <span class="text-text-main font-medium"><?= htmlspecialchars($team['name']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Available Teams Section -->
        <?php if (!empty($availableTeams)): ?>
        <div>
            <h4 class="text-sm font-semibold text-text-muted uppercase tracking-wide mb-3">
                Available Teams (<?= count($availableTeams) ?>)
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($availableTeams as $team): ?>
                    <label class="flex items-center gap-3 p-3 bg-background rounded-sm cursor-pointer border border-transparent hover:border-primary/50 transition-colors">
                        <input type="checkbox" name="teamIds[]" value="<?= htmlspecialchars($team['id']) ?>"
                               class="accent-primary w-5 h-5 team-checkbox">
                        <span class="inline-block w-3 h-3 rounded-full"
                              style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>;"></span>
                        <span class="text-text-main font-medium"><?= htmlspecialchars($team['name']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Original flat grid (used on create page or when showGrouped is false) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4">
            <?php foreach ($availableTeams as $team): ?>
                <?php $isChecked = in_array($team['id'], $selectedTeamIds); ?>
                <label class="flex items-center gap-3 p-3 bg-background rounded-sm cursor-pointer border border-transparent hover:border-primary/50 transition-colors">
                    <input type="checkbox" name="teamIds[]" value="<?= htmlspecialchars($team['id']) ?>"
                           class="accent-primary w-5 h-5 team-checkbox"
                           <?= $isChecked ? 'checked' : '' ?>>
                    <span class="inline-block w-3 h-3 rounded-full"
                          style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>;"></span>
                    <span class="text-text-main font-medium"><?= htmlspecialchars($team['name']) ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($showCount): ?>
<script>
    // Update team count dynamically
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.team-checkbox');
        const countElement = document.getElementById('team-count');

        function updateCount() {
            const checked = document.querySelectorAll('.team-checkbox:checked').length;
            countElement.textContent = checked;
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
        updateCount();
    });
</script>
<?php endif; ?>
