<div class="">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold m-0">Create Cup</h1>
        <a href="<?=$basePath?>/admin/cups" class="btn btn-secondary">Cancel</a>
    </div>

    <?php if (empty($seasons)): ?>
        <div
            class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-500 p-6 rounded-md mb-6 flex items-start flex-col gap-4">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="font-semibold text-lg">Season Required</div>
            </div>
            <p>You need to create a season before you can create a cup.</p>
            <a href="<?=$basePath?>/admin/seasons/create" class="btn btn-primary">Create Season</a>
        </div>
    <?php elseif (empty($teams) || count($teams) < 2): ?>
        <div
            class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-500 p-6 rounded-md mb-6 flex items-start flex-col gap-4">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="font-semibold text-lg">More Teams Required</div>
            </div>
            <p>You need at least 2 teams before you can create a cup.</p>
            <a href="<?=$basePath?>/admin/teams/create" class="btn btn-primary">Add Teams</a>
        </div>
    <?php else: ?>
        <div class="card">
            <form method="POST" action="<?=$basePath?>/admin/cups/store">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-text-muted mb-2">Cup Name <span
                                class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-input" required aria-required="true" placeholder="e.g. FA Cup">
                    </div>

                    <div>
                        <label for="seasonId" class="block text-sm font-medium text-text-muted mb-2">Season <span
                                class="text-danger">*</span></label>
                        <select id="seasonId" name="seasonId" class="form-input" required aria-required="true">
                            <option value="">Select a season</option>
                            <?php foreach ($seasons as $season): ?>
                                <option value="<?= htmlspecialchars($season['id']) ?>">
                                    <?= htmlspecialchars($season['name']) ?>
                                    <?= $season['isActive'] ? '(Active)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pt-6 border-t border-border">
                    <div class="col-span-full mb-2">
                        <h3 class="text-lg font-bold text-text-main">Scheduling Defaults</h3>
                        <p class="text-sm text-text-muted">These settings will be used to automatically schedule the first
                            round.</p>
                    </div>

                    <div>
                        <label for="startDate" class="block text-sm font-medium text-text-muted mb-2">First Fixture
                            Date</label>
                        <input type="date" id="startDate" name="startDate" value="<?= date('Y-m-d') ?>" class="form-input">
                    </div>
                    <div>
                        <label for="frequency" class="block text-sm font-medium text-text-muted mb-2">Match
                            Frequency</label>
                        <select id="frequency" name="frequency" class="form-input">
                            <option value="weekly">Weekly</option>
                            <option value="fortnightly">Fortnightly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div>
                        <label for="matchTime" class="block text-sm font-medium text-text-muted mb-2">Typical Match
                            Time</label>
                        <input type="time" id="matchTime" name="matchTime" value="15:00" class="form-input">
                    </div>
                </div>

                <div class="mb-8 pt-6 border-t border-border">
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-lg font-bold text-text-main">Select Teams <span
                                class="text-sm font-normal text-text-muted ml-2">(minimum 2)</span></label>
                        <div class="text-sm text-text-muted">Teams selected: <span id="team-count"
                                class="font-bold text-primary">0</span></div>
                    </div>
                    <p class="text-sm text-text-muted mb-6">Select the teams to include. Teams will be randomly drawn into
                        the bracket.</p>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-[400px] overflow-y-auto p-2 bg-surface-hover/10 rounded border border-border">
                        <?php foreach ($teams as $team): ?>
                            <label
                                class="flex items-center gap-3 p-3 bg-surface rounded border border-border hover:border-primary/50 hover:bg-surface-hover/50 cursor-pointer transition-all">
                                <input type="checkbox" name="teamIds[]" value="<?= htmlspecialchars($team['id']) ?>"
                                    class="team-checkbox w-4 h-4 accent-primary rounded cursor-pointer">
                                <span class="inline-block w-3 h-3 rounded-full flex-shrink-0"
                                    style="background-color: <?= htmlspecialchars($team['colour'] ?? '#1a5f2a') ?>;"></span>
                                <span class="text-sm font-medium truncate"><?= htmlspecialchars($team['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-6 border-t border-border">
                    <button type="submit" class="btn btn-primary">Create Cup</button>
                    <a href="<?=$basePath?>/admin/cups" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <script>
        // Update selected team count
        const checkboxes = document.querySelectorAll('.team-checkbox');
        const countSpan = document.getElementById('team-count');

        function updateCount() {
            const count = document.querySelectorAll('.team-checkbox:checked').length;
            countSpan.textContent = count;
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
        updateCount(); // Initial count
    </script>
</div>