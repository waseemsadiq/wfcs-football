<?php
/**
 * Season Selector Options
 *
 * Generates <option> elements for a season dropdown.
 *
 * @var array $seasons - Array of season records
 * @var string|int|null $selected - Selected season ID (optional)
 */

foreach ($seasons as $season):
    $isSelected = isset($selected) && $selected == $season['id'];
    $isActive = $season['isActive'] ?? $season['is_active'] ?? false;
?>
    <option value="<?= htmlspecialchars($season['id']) ?>" <?= $isSelected ? 'selected' : '' ?>>
        <?= htmlspecialchars($season['name']) ?>
        <?= $isActive ? ' (Active)' : '' ?>
    </option>
<?php endforeach; ?>
