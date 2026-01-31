<?php
// Sort fixtures by date desc, then time
usort($fixtures, function ($a, $b) {
    if ($a['date'] === $b['date']) {
        return strcmp($a['time'], $b['time']);
    }
    return strcmp($a['date'], $b['date']);
});
?>

<div class="text-center mb-12">
    <h1
        class="text-4xl font-extrabold tracking-tight mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">
        <?= htmlspecialchars($league['name']) ?>
    </h1>
    <h2 class="text-2xl font-bold mb-6 text-text-muted">Fixtures</h2>
</div>

<div class="card">
    <div class="flex justify-end gap-4 mb-8">
        <a href="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>" class="btn btn-secondary">
            Back to League
        </a>
    </div>

    <!-- Regenerate Modal -->
    <div id="regenerateModal"
        class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm items-center justify-center z-50 p-4">
        <div class="bg-surface p-10 rounded-md w-full max-w-lg shadow-glow border border-border relative">
            <h2 class="text-2xl font-bold mb-4 mt-0">Regenerate Fixtures</h2>
            <p class="text-text-muted mb-8">Note: Played fixtures will be preserved. Unplayed fixtures will be replaced
                with a new schedule.</p>

            <form id="regenerateForm" method="POST"
                action="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/regenerate-fixtures">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="ajax" value="1">

                <div id="modal-message" class="hidden p-4 rounded-sm mb-6"></div>

                <div id="modal-fields">
                    <div class="mb-6">
                        <label for="modal-startDate"
                            class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">First
                            Fixture Date</label>
                        <input type="date" id="modal-startDate" name="startDate"
                            value="<?= htmlspecialchars($league['startDate'] ?? '') ?>" required class="form-input">
                    </div>

                    <div class="mb-6">
                        <label for="modal-frequency"
                            class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Match
                            Frequency</label>
                        <select id="modal-frequency" name="frequency" required class="form-input">
                            <option value="weekly" <?= ($league['frequency'] ?? 'weekly') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                            <option value="fortnightly" <?= ($league['frequency'] ?? '') === 'fortnightly' ? 'selected' : '' ?>>Fortnightly</option>
                            <option value="monthly" <?= ($league['frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>
                                Monthly</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="modal-matchTime"
                            class="block mb-2 font-semibold text-text-muted text-sm uppercase tracking-wide">Typical
                            Match Time</label>
                        <input type="time" id="modal-matchTime" name="matchTime"
                            value="<?= htmlspecialchars($league['matchTime'] ?? '15:00') ?>" required
                            class="form-input">
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-10">
                    <button type="button" id="modal-cancel" onclick="handleModalClose()"
                        class="btn btn-secondary">Cancel</button>
                    <button type="submit" id="modal-submit" class="btn btn-primary">Regenerate Now</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($fixtures)): ?>
        <div class="text-center p-12">
            <p class="text-text-muted">No fixtures scheduled yet.</p>
        </div>
    <?php else: ?>
        <?php
        $groupedFixtures = [];
        foreach ($fixtures as $fixture) {
            $date = $fixture['date'];
            if (!isset($groupedFixtures[$date])) {
                $groupedFixtures[$date] = [];
            }
            $groupedFixtures[$date][] = $fixture;
        }
        ?>

        <?php foreach ($groupedFixtures as $date => $dateFixtures): ?>
            <div class="mb-12">
                <h3 class="bg-surface-hover py-3 px-6 -mx-8 mb-6 border-y border-border text-lg text-primary font-bold">
                    <?= date('l, j F Y', strtotime($date)) ?>
                </h3>

                <div class="grid gap-4">
                    <?php foreach ($dateFixtures as $fixture): ?>
                        <div class="bg-background border border-border rounded-sm p-6">
                            <form method="POST"
                                action="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/fixtures">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="fixtureId" value="<?= htmlspecialchars($fixture['id']) ?>">

                                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-8 mb-4">
                                    <!-- Home Team -->
                                    <div class="text-right flex items-center justify-end gap-4">
                                        <strong class="text-lg"><?= htmlspecialchars($fixture['homeTeamName']) ?></strong>
                                        <span class="inline-block w-4 h-4 rounded bg-current"
                                            style="color: <?= htmlspecialchars($fixture['homeTeamColour'] ?? '#000') ?>;"></span>
                                    </div>

                                    <!-- Scores -->
                                    <div class="flex items-center gap-4 bg-surface px-4 py-2 rounded-sm border border-border">
                                        <input type="number" name="homeScore" min="0" max="99"
                                            class="w-12 text-center p-1 text-xl font-bold text-primary bg-transparent border-none focus:ring-0 appearance-none"
                                            value="<?= $fixture['result'] !== null ? htmlspecialchars((string) $fixture['result']['homeScore']) : '' ?>"
                                            placeholder="-">
                                        <span class="text-text-muted font-bold">vs</span>
                                        <input type="number" name="awayScore" min="0" max="99"
                                            class="w-12 text-center p-1 text-xl font-bold text-primary bg-transparent border-none focus:ring-0 appearance-none"
                                            value="<?= $fixture['result'] !== null ? htmlspecialchars((string) $fixture['result']['awayScore']) : '' ?>"
                                            placeholder="-">
                                    </div>

                                    <!-- Away Team -->
                                    <div class="text-left flex items-center justify-start gap-4">
                                        <span class="inline-block w-4 h-4 rounded bg-current"
                                            style="color: <?= htmlspecialchars($fixture['awayTeamColour'] ?? '#000') ?>;"></span>
                                        <strong class="text-lg"><?= htmlspecialchars($fixture['awayTeamName']) ?></strong>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center border-t border-border pt-4">
                                    <div class="flex gap-4 text-text-muted text-sm">
                                        <span>Time: <input type="time" name="time" value="<?= htmlspecialchars($fixture['time']) ?>"
                                                class="bg-transparent border border-border text-text-main p-1 rounded w-auto focus:border-primary focus:outline-none"></span>
                                        <span>Date: <input type="date" name="date" value="<?= htmlspecialchars($fixture['date']) ?>"
                                                class="bg-transparent border border-border text-text-main p-1 rounded w-auto focus:border-primary focus:outline-none"></span>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                </div>

                                <details class="mt-4">
                                    <summary class="cursor-pointer text-text-muted text-sm">Match Details (Scorers & Cards)
                                    </summary>
                                    <div class="grid grid-cols-2 gap-8 mt-4 bg-surface p-6 rounded-sm">
                                        <div>
                                            <label
                                                class="block mb-2 text-xs uppercase tracking-wide font-semibold text-text-muted">Home
                                                Details</label>
                                            <input type="text" name="homeScorers" placeholder="Scorers (e.g. Smith 23')"
                                                value="<?= htmlspecialchars($fixture['result']['homeScorers'] ?? '') ?>"
                                                class="form-input mb-2 text-sm py-2">
                                            <input type="text" name="homeCards" placeholder="Cards (e.g. Wilson (Y))"
                                                value="<?= htmlspecialchars($fixture['result']['homeCards'] ?? '') ?>"
                                                class="form-input text-sm py-2">
                                        </div>
                                        <div>
                                            <label
                                                class="block mb-2 text-xs uppercase tracking-wide font-semibold text-text-muted">Away
                                                Details</label>
                                            <input type="text" name="awayScorers" placeholder="Scorers (e.g. Brown 45')"
                                                value="<?= htmlspecialchars($fixture['result']['awayScorers'] ?? '') ?>"
                                                class="form-input mb-2 text-sm py-2">
                                            <input type="text" name="awayCards" placeholder="Cards (e.g. Taylor (R))"
                                                value="<?= htmlspecialchars($fixture['result']['awayCards'] ?? '') ?>"
                                                class="form-input text-sm py-2">
                                        </div>
                                    </div>
                                </details>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="mt-8 pt-6 border-t border-border">
        <button type="button"
            onclick="document.getElementById('regenerateModal').classList.remove('hidden'); document.getElementById('regenerateModal').classList.add('flex');"
            class="btn bg-orange-500 text-black hover:bg-orange-600 font-bold border-none w-full sm:w-auto">
            Regenerate Fixtures
        </button>
    </div>
</div>

<script>
    let isRegenerated = false;

    function handleModalClose() {
        if (isRegenerated) {
            window.location.reload();
        } else {
            document.getElementById('regenerateModal').classList.add('hidden');
            document.getElementById('regenerateModal').classList.remove('flex');
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
                    fieldsDiv.style.display = 'none'; // Hide fields
                    submitBtn.style.display = 'none'; // Hide button
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