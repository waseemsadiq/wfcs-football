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

// Load club colours from JSON (static reference data for colour picker)
$clubColoursJson = file_get_contents(BASE_PATH . '/css/club-colours.json');
$clubColours = json_decode($clubColoursJson, true);
?>

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
    <button type="button" id="quick-pick-toggle"
        class="group inline-flex items-center gap-2 px-3 py-2 text-[13px] font-medium text-text-muted bg-transparent border border-border rounded-md cursor-pointer transition-all duration-200 hover:text-primary hover:border-primary hover:bg-primary/5">
        Quick Pick Team Colour
        <svg class="transition-transform duration-200 group-[.open]:rotate-180" width="12" height="12"
            viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <path d="M2 4l4 4 4-4" />
        </svg>
    </button>

    <!-- Quick Pick Panel -->
    <div id="quick-pick-panel"
        class="max-h-0 overflow-hidden transition-[max-height,padding] duration-300 ease-out [&.open]:max-h-[500px] [&.open]:pt-4">
        <?php foreach ($clubColours['leagues'] as $league): ?>
            <div class="mb-8">
                <div class="text-[11px] font-semibold uppercase tracking-[0.05em] text-text-muted mb-2.5">
                    <?= htmlspecialchars($league['name']) ?>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($league['teams'] as $club): ?>
                        <button type="button"
                            class="club-chip inline-flex items-center gap-1.5 px-1.5 py-1.5 pr-2.5 text-xs font-medium text-text-muted bg-surface-hover border border-border rounded-[20px] cursor-pointer transition-all duration-150 whitespace-nowrap hover:text-text-main hover:border-primary hover:bg-primary/10 [&.selected]:text-text-main [&.selected]:border-primary [&.selected]:bg-primary/15 [&.selected]:ring-2 [&.selected]:ring-primary/30"
                            data-color="<?= htmlspecialchars($club['colour']) ?>">
                            <span class="w-4 h-4 rounded-full shrink-0 shadow-inner"
                                style="background-color: <?= htmlspecialchars($club['colour']) ?>"></span>
                            <?= htmlspecialchars($club['name']) ?>
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
        const chips = document.querySelectorAll('.club-chip');

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