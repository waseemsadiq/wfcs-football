<div class="max-w-5xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold m-0 tracking-tight"><?= htmlspecialchars($cup['name']) ?></h1>
            <p class="text-text-muted mt-1 text-lg">Manage Fixtures</p>
        </div>
        <div class="flex gap-4">
            <a href="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>" class="btn btn-secondary">Back to
                Bracket</a>
        </div>
    </div>

    <div class="card mb-8">
        <div class="mb-6 pb-6 border-b border-border">
            <p class="text-text-muted">Enter results for each fixture. Knockout matches support extra time and penalties
                if scores are level.</p>
        </div>

        <?php if (empty($rounds)): ?>
            <div class="text-center py-12 text-text-muted">
                <p>No fixtures scheduled yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($rounds as $round): ?>
                <div class="mb-10 last:mb-0">
                    <h3
                        class="bg-surface-hover/50 py-2 px-4 -mx-6 mb-6 border-y border-border text-center font-bold text-lg text-text-main">
                        <?= htmlspecialchars($round['name']) ?>
                    </h3>

                    <?php foreach ($round['fixtures'] as $fixture): ?>
                        <?php if ($fixture['homeTeamId'] && $fixture['awayTeamId']): ?>
                            <div
                                class="bg-surface border border-border rounded-lg mb-4 p-4 hover:border-primary/30 transition-colors shadow-sm">
                                <form method="POST" action="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/fixtures">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="fixtureId" value="<?= htmlspecialchars($fixture['id']) ?>">

                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center mb-4">
                                        <!-- Home Team -->
                                        <div class="lg:col-span-3 flex items-center justify-end gap-3 text-right order-1 lg:order-1">
                                            <strong
                                                class="text-sm md:text-base"><?= htmlspecialchars($fixture['homeTeamName']) ?></strong>
                                            <span class="inline-block w-4 h-4 rounded-sm flex-shrink-0 shadow-sm"
                                                style="background-color: <?= htmlspecialchars($fixture['homeTeamColour'] ?? '#000') ?>;"></span>
                                        </div>

                                        <!-- Scores -->
                                        <div class="lg:col-span-3 flex items-center justify-center gap-2 order-2 lg:order-2">
                                            <input type="number" name="homeScore" min="0" max="99"
                                                class="form-input w-16 text-center font-bold p-1"
                                                value="<?= $fixture['result'] !== null ? htmlspecialchars((string) $fixture['result']['homeScore']) : '' ?>"
                                                placeholder="-">
                                            <span class="text-text-muted font-bold">-</span>
                                            <input type="number" name="awayScore" min="0" max="99"
                                                class="form-input w-16 text-center font-bold p-1"
                                                value="<?= $fixture['result'] !== null ? htmlspecialchars((string) $fixture['result']['awayScore']) : '' ?>"
                                                placeholder="-">
                                        </div>

                                        <!-- Away Team -->
                                        <div class="lg:col-span-3 flex items-center justify-start gap-3 order-3 lg:order-3">
                                            <span class="inline-block w-4 h-4 rounded-sm flex-shrink-0 shadow-sm"
                                                style="background-color: <?= htmlspecialchars($fixture['awayTeamColour'] ?? '#000') ?>;"></span>
                                            <strong
                                                class="text-sm md:text-base"><?= htmlspecialchars($fixture['awayTeamName']) ?></strong>
                                        </div>

                                        <!-- Actions/Date -->
                                        <div
                                            class="lg:col-span-3 flex flex-col sm:flex-row items-center justify-end gap-2 order-4 lg:order-4">
                                            <div class="flex gap-1 w-full sm:w-auto">
                                                <input type="date" name="date" class="form-input py-1 px-2 text-xs"
                                                    style="width: 100px;" value="<?= htmlspecialchars($fixture['date']) ?>">
                                                <input type="time" name="time" class="form-input py-1 px-2 text-xs" style="width: 80px;"
                                                    value="<?= htmlspecialchars($fixture['time']) ?>">
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary w-full sm:w-auto">Save</button>
                                        </div>
                                    </div>

                                    <div class="border-t border-border/50 pt-3 mt-3">
                                        <!-- Extra Time / Penalties -->
                                        <details class="group mb-2" <?= ($fixture['result']['extraTime'] ?? false) || ($fixture['result']['penalties'] ?? false) ? 'open' : '' ?>>
                                            <summary
                                                class="cursor-pointer text-xs font-medium text-text-muted hover:text-primary transition-colors select-none flex items-center gap-1 w-max">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 transition-transform group-open:rotate-90" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                                Extra Time / Penalties
                                            </summary>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 bg-surface-hover/30 p-4 rounded text-sm">
                                                <?php
                                                // Determine initial toggle state based on existing data
                                                $hasETData = ($fixture['result'] !== null) && (isset($fixture['result']['homeScoreET']) || isset($fixture['result']['awayScoreET']));
                                                $hasPenData = ($fixture['result'] !== null) && (isset($fixture['result']['homePens']) || isset($fixture['result']['awayPens']));
                                                ?>
                                                <!-- Extra Time -->
                                                <div>
                                                    <label class="flex items-center gap-3 mb-2 font-medium cursor-pointer">
                                                        <div class="relative inline-block w-11 h-6">
                                                            <input type="checkbox" name="extraTime" value="1"
                                                                class="sr-only peer"
                                                                id="extraTimeToggle-<?= $fixture['id'] ?>"
                                                                role="switch"
                                                                aria-checked="<?= $hasETData ? 'true' : 'false' ?>"
                                                                aria-labelledby="extraTimeLabel-<?= $fixture['id'] ?>"
                                                                <?= $hasETData ? 'checked' : '' ?>>
                                                            <div class="w-11 h-6 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors peer-focus:ring-2 peer-focus:ring-primary/50"></div>
                                                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                                                        </div>
                                                        <span id="extraTimeLabel-<?= $fixture['id'] ?>">Went to Extra Time</span>
                                                    </label>
                                                    <div class="et-inputs transition-all duration-200"
                                                         id="etInputs-<?= $fixture['id'] ?>"
                                                         style="<?= $hasETData ? '' : 'display: none;' ?>">
                                                        <div class="flex items-center gap-2 ml-6">
                                                            <span class="text-text-muted text-xs">Score after ET:</span>
                                                            <input type="number" name="homeScoreET" min="0" max="99"
                                                                class="form-input w-12 text-center py-1 px-1 text-xs"
                                                                value="<?= $fixture['result']['homeScoreET'] ?? '' ?>" placeholder="-">
                                                            <span class="text-text-muted">-</span>
                                                            <input type="number" name="awayScoreET" min="0" max="99"
                                                                class="form-input w-12 text-center py-1 px-1 text-xs"
                                                                value="<?= $fixture['result']['awayScoreET'] ?? '' ?>" placeholder="-">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Penalties -->
                                                <div>
                                                    <label class="flex items-center gap-3 mb-2 font-medium cursor-pointer">
                                                        <div class="relative inline-block w-11 h-6">
                                                            <input type="checkbox" name="penalties" value="1"
                                                                class="sr-only peer"
                                                                id="penaltiesToggle-<?= $fixture['id'] ?>"
                                                                role="switch"
                                                                aria-checked="<?= $hasPenData ? 'true' : 'false' ?>"
                                                                aria-labelledby="penaltiesLabel-<?= $fixture['id'] ?>"
                                                                <?= $hasPenData ? 'checked' : '' ?>>
                                                            <div class="w-11 h-6 bg-gray-600 rounded-full peer-checked:bg-primary transition-colors peer-focus:ring-2 peer-focus:ring-primary/50"></div>
                                                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
                                                        </div>
                                                        <span id="penaltiesLabel-<?= $fixture['id'] ?>">Penalties</span>
                                                    </label>
                                                    <div class="pen-inputs transition-all duration-200"
                                                         id="penInputs-<?= $fixture['id'] ?>"
                                                         style="<?= $hasPenData ? '' : 'display: none;' ?>">
                                                        <div class="flex items-center gap-2 ml-6">
                                                            <span class="text-text-muted text-xs">Penalty Score:</span>
                                                            <input type="number" name="homePens" min="0" max="99"
                                                                class="form-input w-12 text-center py-1 px-1 text-xs"
                                                                value="<?= $fixture['result']['homePens'] ?? '' ?>" placeholder="-">
                                                            <span class="text-text-muted">-</span>
                                                            <input type="number" name="awayPens" min="0" max="99"
                                                                class="form-input w-12 text-center py-1 px-1 text-xs"
                                                                value="<?= $fixture['result']['awayPens'] ?? '' ?>" placeholder="-">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>

                                        <!-- Match Details -->
                                        <details class="group">
                                            <summary
                                                class="cursor-pointer text-xs font-medium text-text-muted hover:text-primary transition-colors select-none flex items-center gap-1 w-max">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 transition-transform group-open:rotate-90" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                                Match Details (Scorers, Cards)
                                            </summary>
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 bg-surface-hover/30 p-4 rounded text-sm">
                                                <div>
                                                    <label class="block text-xs text-text-muted mb-1">Home Scorers</label>
                                                    <input type="text" name="homeScorers" class="form-input py-1 px-2 text-xs w-full"
                                                        placeholder="e.g. Smith 23', Jones 67'"
                                                        value="<?= htmlspecialchars($fixture['result']['homeScorers'] ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-text-muted mb-1">Away Scorers</label>
                                                    <input type="text" name="awayScorers" class="form-input py-1 px-2 text-xs w-full"
                                                        placeholder="e.g. Brown 45'"
                                                        value="<?= htmlspecialchars($fixture['result']['awayScorers'] ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-text-muted mb-1">Home Cards</label>
                                                    <input type="text" name="homeCards" class="form-input py-1 px-2 text-xs w-full"
                                                        placeholder="e.g. Wilson (Y)"
                                                        value="<?= htmlspecialchars($fixture['result']['homeCards'] ?? '') ?>">
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-text-muted mb-1">Away Cards</label>
                                                    <input type="text" name="awayCards" class="form-input py-1 px-2 text-xs w-full"
                                                        placeholder="e.g. Taylor (R)"
                                                        value="<?= htmlspecialchars($fixture['result']['awayCards'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </details>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <div
                                class="border border-dashed border-border rounded-lg mb-4 p-8 text-center text-text-muted bg-surface-hover/10">
                                <span class="block mb-1">Match not determined yet</span>
                                <span class="text-xs opacity-75">Waiting for previous round results</span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="mt-8 pt-6 border-t border-border">
            <button type="button"
                onclick="document.getElementById('regenerateModal').classList.remove('hidden'); document.getElementById('regenerateModal').classList.add('flex');"
                class="btn btn-primary bg-yellow-600 hover:bg-yellow-700 border-yellow-600 hover:border-yellow-700 text-white w-full sm:w-auto">
                Regenerate Fixtures
            </button>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="regenerateModal"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm items-center justify-center z-50 p-4">
    <div
        class="bg-surface border border-border rounded-xl shadow-2xl max-w-lg w-full transform transition-all p-6 relative">
        <h2 class="text-2xl font-bold mb-2">Regenerate Cup Fixtures</h2>
        <p class="text-text-muted mb-6">If results exist, only unplayed fixtures will be rescheduled. Otherwise, the
            bracket will be re-drawn.</p>

        <form id="regenerateForm" method="POST"
            action="<?=$basePath?>/admin/cups/<?= htmlspecialchars($cup['slug'] ?? $cup['id']) ?>/regenerate-fixtures">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="ajax" value="1">

            <div id="modal-message" class="hidden p-4 rounded mb-4 text-sm font-medium"></div>

            <div id="modal-fields" class="space-y-4">
                <div>
                    <label for="modal-startDate" class="block text-sm font-medium text-text-muted mb-1">First round
                        Date</label>
                    <input type="date" id="modal-startDate" name="startDate"
                        value="<?= htmlspecialchars($cup['startDate'] ?? date('Y-m-d')) ?>" class="form-input" required>
                </div>

                <div>
                    <label for="modal-frequency" class="block text-sm font-medium text-text-muted mb-1">Round
                        Frequency</label>
                    <select id="modal-frequency" name="frequency" class="form-input" required>
                        <option value="weekly" <?= ($cup['frequency'] ?? 'weekly') === 'weekly' ? 'selected' : '' ?>>Weekly
                        </option>
                        <option value="fortnightly" <?= ($cup['frequency'] ?? '') === 'fortnightly' ? 'selected' : '' ?>>
                            Fortnightly</option>
                        <option value="monthly" <?= ($cup['frequency'] ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly
                        </option>
                    </select>
                </div>

                <div>
                    <label for="modal-matchTime" class="block text-sm font-medium text-text-muted mb-1">Typical Match
                        Time</label>
                    <input type="time" id="modal-matchTime" name="matchTime"
                        value="<?= htmlspecialchars($cup['matchTime'] ?? '15:00') ?>" class="form-input" required>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-border">
                <button type="button" id="modal-cancel" onclick="handleModalClose()"
                    class="btn btn-secondary">Cancel</button>
                <button type="submit" id="modal-submit"
                    class="btn btn-primary bg-yellow-600 hover:bg-yellow-700 border-yellow-600 hover:border-yellow-700 text-white">Regenerate
                    Now</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle switch show/hide logic
    document.addEventListener('DOMContentLoaded', function() {
        // Handle all ET toggles
        document.querySelectorAll('[id^="extraTimeToggle-"]').forEach(toggle => {
            const fixtureId = toggle.id.replace('extraTimeToggle-', '');
            const inputs = document.getElementById('etInputs-' + fixtureId);

            toggle.addEventListener('change', function() {
                if (this.checked) {
                    inputs.style.display = '';
                } else {
                    inputs.style.display = 'none';
                }
            });
        });

        // Handle all penalty toggles
        document.querySelectorAll('[id^="penaltiesToggle-"]').forEach(toggle => {
            const fixtureId = toggle.id.replace('penaltiesToggle-', '');
            const inputs = document.getElementById('penInputs-' + fixtureId);

            toggle.addEventListener('change', function() {
                if (this.checked) {
                    inputs.style.display = '';
                } else {
                    inputs.style.display = 'none';
                }
            });
        });
    });

    let isRegenerated = false;

    function handleModalClose() {
        if (isRegenerated) {
            window.location.reload();
        } else {
            const modal = document.getElementById('regenerateModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    document.getElementById('regenerateForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const submitBtn = document.getElementById('modal-submit');
        const messageDiv = document.getElementById('modal-message');
        const fieldsDiv = document.getElementById('modal-fields');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="animate-spin inline-block mr-2 text-white">&#9696;</span> Regenerating...';

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
                    messageDiv.className = 'p-4 rounded mb-4 text-sm font-medium bg-green-500/10 text-green-500 border border-green-500/20';
                    messageDiv.style.display = 'block';
                    fieldsDiv.style.display = 'none';
                    submitBtn.style.display = 'none';
                    document.getElementById('modal-cancel').textContent = 'Close & Refresh';
                    document.getElementById('modal-cancel').className = 'btn btn-primary';
                } else {
                    messageDiv.textContent = data.error || 'An error occurred.';
                    messageDiv.className = 'p-4 rounded mb-4 text-sm font-medium bg-red-500/10 text-red-500 border border-red-500/20';
                    messageDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Regenerate Now';
                }
            })
            .catch(error => {
                messageDiv.textContent = 'A network error occurred.';
                messageDiv.className = 'p-4 rounded mb-4 text-sm font-medium bg-red-500/10 text-red-500 border border-red-500/20';
                messageDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Regenerate Now';
            });
    });
</script>