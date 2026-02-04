<?php
/**
 * Team Colour Picker Partial
 * 
 * Includes a colour input with quick-pick team colour swatches.
 * 
 * Required variables:
 * - $colourValue: Current colour value (hex string, e.g. '#1a5f2a')
 * - $basePath: Application base path (for loading JSON)
 */

$colourValue = $colourValue ?? '#1a5f2a';

// Load team colours from JSON (static reference data for colour picker)
$teamColoursJson = file_get_contents(BASE_PATH . '/css/team-colours.json');
$teamColours = json_decode($teamColoursJson, true);
?>

<style>
    .team-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px 6px 6px;
        font-size: 12px;
        font-weight: 500;
        color: var(--color-text-muted);
        background: var(--color-surface-hover);
        border: 1px solid var(--color-border);
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .team-chip:hover {
        color: var(--color-text-main);
        border-color: var(--color-primary);
        background: rgba(74, 222, 128, 0.1);
    }

    :root.light .team-chip:hover {
        background: rgba(69, 162, 218, 0.1);
    }

    .team-chip.selected {
        color: var(--color-text-main);
        border-color: var(--color-primary);
        background: rgba(74, 222, 128, 0.15);
        box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.3);
    }

    :root.light .team-chip.selected {
        background: rgba(69, 162, 218, 0.15);
        box-shadow: 0 0 0 2px rgba(69, 162, 218, 0.3);
    }

    .team-chip .dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        flex-shrink: 0;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .quick-pick-panel {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }

    .quick-pick-panel.open {
        max-height: 500px;
        padding-top: 16px;
    }

    .quick-pick-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 500;
        color: var(--color-text-muted);
        background: transparent;
        border: 1px solid var(--color-border);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .quick-pick-toggle:hover {
        color: var(--color-primary);
        border-color: var(--color-primary);
        background: rgba(74, 222, 128, 0.05);
    }

    :root.light .quick-pick-toggle:hover {
        background: rgba(69, 162, 218, 0.05);
    }

    .quick-pick-toggle .arrow {
        transition: transform 0.2s;
    }

    .quick-pick-toggle.open .arrow {
        transform: rotate(180deg);
    }

    .league-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-text-muted);
        margin-bottom: 10px;
    }
</style>

<div class="mb-6">
    <label for="colour" class="block text-sm font-medium text-text-muted mb-2">Team Colour</label>

    <!-- Colour Input Row -->
    <div class="flex items-center gap-4 mb-3">
        <input type="color" id="colour" name="colour" value="<?= htmlspecialchars($colourValue) ?>"
            class="h-10 w-20 p-1 bg-background border border-border rounded cursor-pointer">
        <span id="colour-value" class="font-mono text-text-muted">
            <?= htmlspecialchars($colourValue) ?>
        </span>
    </div>

    <!-- Quick Pick Toggle -->
    <button type="button" id="quick-pick-toggle" class="quick-pick-toggle">
        Quick Pick Team Colour
        <svg class="arrow" width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 4l4 4 4-4" />
        </svg>
    </button>

    <!-- Quick Pick Panel -->
    <div id="quick-pick-panel" class="quick-pick-panel">
        <?php foreach ($teamColours['leagues'] as $league): ?>
            <div class="mb-8">
                <div class="league-label">
                    <?= htmlspecialchars($league['name']) ?>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($league['teams'] as $team): ?>
                        <button type="button" class="team-chip" data-color="<?= htmlspecialchars($team['colour']) ?>">
                            <span class="dot" style="background-color: <?= htmlspecialchars($team['colour']) ?>"></span>
                            <?= htmlspecialchars($team['name']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p class="mt-3 text-sm text-text-muted">Used to identify this team in fixtures and tables.</p>
</div>

<script>
    (function () {
        // Quick pick toggle
        const toggleBtn = document.getElementById('quick-pick-toggle');
        const panel = document.getElementById('quick-pick-panel');

        toggleBtn.addEventListener('click', () => {
            toggleBtn.classList.toggle('open');
            panel.classList.toggle('open');
        });

        // Colour picker sync
        const colourInput = document.getElementById('colour');
        const colourValue = document.getElementById('colour-value');
        const chips = document.querySelectorAll('.team-chip');

        colourInput.addEventListener('input', (e) => {
            colourValue.textContent = e.target.value;
            updateSelectedChip(e.target.value);
        });

        chips.forEach(chip => {
            chip.addEventListener('click', () => {
                const color = chip.dataset.color;
                colourInput.value = color;
                colourValue.textContent = color;
                updateSelectedChip(color);
            });
        });

        function updateSelectedChip(color) {
            chips.forEach(c => {
                c.classList.toggle('selected', c.dataset.color.toLowerCase() === color.toLowerCase());
            });
        }

        // Set initial selected state
        updateSelectedChip(colourInput.value);
    })();
</script>