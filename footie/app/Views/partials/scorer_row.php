<?php
/**
 * Scorer row partial for fixture editing.
 *
 * Required variables:
 * - $side: 'home' or 'away'
 * - $index: Row index
 * - $players: Array of player data for the team
 * - $scorer: Existing scorer data (optional)
 */
$scorer = $scorer ?? [];
?>
<div class="border border-border rounded p-2 mb-2 bg-surface-hover/30">
    <div class="flex gap-1 items-center mb-1">
        <select name="<?= $side ?>Scorers[<?= $index ?>][player]"
            class="form-input py-1 px-2 text-xs flex-1">
            <option value="">Select player...</option>
            <?php foreach ($players as $player): ?>
                <option value="<?= htmlspecialchars($player['name']) ?>"
                    <?= ($scorer['player'] ?? '') === $player['name'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($player['name']) ?>
                    <?php if (!empty($player['squadNumber'])): ?>
                        (#<?= $player['squadNumber'] ?>)
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="<?= $side ?>Scorers[<?= $index ?>][minute]"
            value="<?= htmlspecialchars((string) ($scorer['minute'] ?? '')) ?>"
            class="form-input py-1 px-1 text-xs w-10 text-center"
            placeholder="Min">
        <label class="flex items-center gap-1 text-[10px] text-text-muted cursor-pointer whitespace-nowrap">
            <input type="checkbox"
                name="<?= $side ?>Scorers[<?= $index ?>][ownGoal]" value="1"
                <?= ($scorer['ownGoal'] ?? false) ? 'checked' : '' ?>> OG
        </label>
        <button type="button"
            class="text-red-500 hover:text-red-400 text-xs px-1"
            onclick="this.parentElement.parentElement.remove()">×</button>
    </div>
    <div class="flex gap-1 items-center">
        <label class="text-[10px] text-text-muted whitespace-nowrap w-12">Assist:</label>
        <select name="<?= $side ?>Scorers[<?= $index ?>][assist]"
            class="form-input py-1 px-2 text-xs flex-1">
            <option value="">None</option>
            <?php foreach ($players as $player): ?>
                <option value="<?= htmlspecialchars($player['name']) ?>"
                    <?= ($scorer['assist'] ?? '') === $player['name'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($player['name']) ?>
                    <?php if (!empty($player['squadNumber'])): ?>
                        (#<?= $player['squadNumber'] ?>)
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
