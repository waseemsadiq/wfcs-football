<?php
/**
 * Card row partial for fixture editing.
 *
 * Required variables:
 * - $side: 'home' or 'away'
 * - $index: Row index
 * - $players: Array of player data for the team
 * - $card: Existing card data (optional)
 */
$card = $card ?? [];
$cardTypes = [
    'sinBins' => 'Sin Bin',
    'blue' => 'Blue',
    'yellow' => 'Yellow',
    'red' => 'Red'
];
?>
<div class="flex gap-1 items-center">
    <select name="<?= $side ?>CardsCombined[<?= $index ?>][type]"
        class="form-input py-1 px-1 text-[10px] w-20">
        <?php foreach ($cardTypes as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= ($card['type'] ?? 'yellow') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
        <?php endforeach; ?>
    </select>
    <select name="<?= $side ?>CardsCombined[<?= $index ?>][player]"
        class="form-input py-1 px-2 text-xs flex-1">
        <option value="">Select player...</option>
        <?php foreach ($players as $player): ?>
            <option value="<?= htmlspecialchars($player['name']) ?>"
                <?= ($card['player'] ?? '') === $player['name'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($player['name']) ?>
                <?php if (!empty($player['squadNumber'])): ?>
                    (#<?= $player['squadNumber'] ?>)
                <?php endif; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="<?= $side ?>CardsCombined[<?= $index ?>][minute]"
        value="<?= htmlspecialchars($card['minute'] ?? '') ?>"
        class="form-input py-1 px-1 text-xs w-10 text-center"
        placeholder="Min">
    <button type="button"
        class="text-red-500 hover:text-red-400 text-xs px-1"
        onclick="this.parentElement.remove()">×</button>
</div>
