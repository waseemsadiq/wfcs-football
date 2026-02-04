<?php
/**
 * Scheduling Fields (Start Date, Frequency, Match Time)
 *
 * Common scheduling fields used in league and cup forms.
 *
 * @var array|null $entity - Existing entity for edit forms (optional)
 * @var bool $required - Whether fields are required (default: true)
 * @var string $labelPrefix - Label prefix text (default: 'First Fixture')
 */

$required = $required ?? true;
$labelPrefix = $labelPrefix ?? 'First Fixture';
$requiredAttr = $required ? 'required aria-required="true"' : '';
$requiredIndicator = $required ? ' *' : '';

$startDate = $entity['startDate'] ?? $entity['start_date'] ?? '';
$frequency = $entity['frequency'] ?? 'weekly';
$matchTime = $entity['matchTime'] ?? $entity['match_time'] ?? '15:00';
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label for="startDate" class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">
            <?= htmlspecialchars($labelPrefix) ?> Date<?= $requiredIndicator ?>
        </label>
        <input type="date" id="startDate" name="startDate" class="form-input"
               value="<?= htmlspecialchars($startDate) ?>" <?= $requiredAttr ?>>
    </div>

    <div>
        <label for="frequency" class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">
            Match Frequency<?= $requiredIndicator ?>
        </label>
        <select id="frequency" name="frequency" class="form-input" <?= $requiredAttr ?>>
            <?php $selected = $frequency; include __DIR__ . '/frequency_select.php'; ?>
        </select>
    </div>

    <div>
        <label for="matchTime" class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">
            Typical Match Time<?= $requiredIndicator ?>
        </label>
        <input type="time" id="matchTime" name="matchTime" class="form-input"
               value="<?= htmlspecialchars($matchTime) ?>" <?= $requiredAttr ?>>
    </div>
</div>
