<?php
/**
 * Frequency Selector Options
 *
 * Generates <option> elements for a frequency dropdown.
 *
 * @var string|null $selected - Selected frequency value (optional, defaults to 'weekly')
 */

$selected = $selected ?? 'weekly';
?>
<option value="weekly" <?= $selected === 'weekly' ? 'selected' : '' ?>>Weekly</option>
<option value="fortnightly" <?= $selected === 'fortnightly' ? 'selected' : '' ?>>Fortnightly</option>
<option value="monthly" <?= $selected === 'monthly' ? 'selected' : '' ?>>Monthly</option>
