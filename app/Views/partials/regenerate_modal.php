<?php
/**
 * Render a regenerate fixtures modal for leagues and cups.
 *
 * Variables expected:
 * @var string $modalId - Unique modal ID
 * @var string $competitionType - 'league' or 'cup'
 * @var string $formAction - POST action URL
 * @var string $csrfToken - CSRF token value
 * @var array $competition - Competition data (startDate, frequency, matchTime)
 */

// Determine the title and labels based on competition type
$isCup = ($competitionType ?? 'league') === 'cup';
$modalTitle = $isCup ? 'Regenerate Cup Fixtures' : 'Regenerate Fixtures';
$startDateLabel = $isCup ? 'First round Date' : 'First Fixture Date';
$frequencyLabel = $isCup ? 'Round Frequency' : 'Match Frequency';
$descriptionText = $isCup
    ? 'If results exist, only unplayed fixtures will be rescheduled. Otherwise, the bracket will be re-drawn.'
    : 'Note: Played fixtures will be preserved. Unplayed fixtures will be replaced with a new schedule.';
$buttonClass = $isCup ? 'bg-yellow-600 hover:bg-yellow-700 border-yellow-600 hover:border-yellow-700' : 'btn-primary';
?>

<!-- Regenerate Modal -->
<div id="<?= htmlspecialchars($modalId) ?>"
    class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm items-center justify-center z-50 p-4">
    <div class="bg-surface p-10 rounded-md w-full max-w-lg shadow-glow border border-border relative">
        <h2 class="text-2xl font-bold mb-4 mt-0"><?= htmlspecialchars($modalTitle) ?></h2>
        <p class="text-text-muted mb-8"><?= htmlspecialchars($descriptionText) ?></p>

        <form id="regenerateForm" method="POST"
            action="<?= htmlspecialchars($formAction) ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="ajax" value="1">

            <div id="modal-message" class="hidden p-4 rounded-sm mb-6"></div>

            <div id="modal-fields">
                <div class="mb-6">
                    <label for="modal-startDate"
                        class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">
                        <?= htmlspecialchars($startDateLabel) ?>
                    </label>
                    <input type="date" id="modal-startDate" name="startDate"
                        value="<?= htmlspecialchars($competition['startDate'] ?? '') ?>" required class="form-input">
                </div>

                <div class="mb-6">
                    <label for="modal-frequency"
                        class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">
                        <?= htmlspecialchars($frequencyLabel) ?>
                    </label>
                    <select id="modal-frequency" name="frequency" required class="form-input">
                        <option value="weekly" <?= ($competition['frequency'] ?? 'weekly') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                        <option value="fortnightly" <?= ($competition['frequency'] ?? '') === 'fortnightly' ? 'selected' : '' ?>>Fortnightly</option>
                        <option value="monthly" <?= ($competition['frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="modal-matchTime"
                        class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Typical Match Time</label>
                    <input type="time" id="modal-matchTime" name="matchTime"
                        value="<?= htmlspecialchars($competition['matchTime'] ?? '15:00') ?>" required
                        class="form-input">
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-10">
                <button type="button" id="modal-cancel" onclick="handleModalClose()"
                    class="btn btn-secondary">Cancel</button>
                <button type="submit" id="modal-submit" class="btn text-white <?= $buttonClass ?>">Regenerate Now</button>
            </div>
        </form>
    </div>
</div>

<script>
    let isRegenerated = false;

    function handleModalClose() {
        if (isRegenerated) {
            window.location.reload();
        } else {
            document.getElementById('<?= htmlspecialchars($modalId) ?>').classList.add('hidden');
            document.getElementById('<?= htmlspecialchars($modalId) ?>').classList.remove('flex');
        }
    }

    document.getElementById('regenerateForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const submitBtn = document.getElementById('modal-submit');
        const messageDiv = document.getElementById('modal-message');
        const fieldsDiv = document.getElementById('modal-fields');

        submitBtn.disabled = true;
        submitBtn.textContent = 'Regenerating...';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    isRegenerated = true;
                    messageDiv.textContent = data.message;
                    messageDiv.className = 'p-4 rounded-sm mb-6 bg-primary/10 text-primary block';
                    fieldsDiv.style.display = 'none';
                    submitBtn.style.display = 'none';
                    document.getElementById('modal-cancel').textContent = 'Close & Refresh';
                } else {
                    messageDiv.textContent = data.error || 'An error occurred.';
                    messageDiv.className = 'p-4 rounded-sm mb-6 bg-danger/10 text-red-300 block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Regenerate Now';
                }
            })
            .catch(error => {
                messageDiv.textContent = 'A network error occurred.';
                messageDiv.className = 'p-4 rounded-sm mb-6 bg-danger/10 text-red-300 block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Regenerate Now';
            });
    });
</script>
