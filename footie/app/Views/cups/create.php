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
                            <?php include __DIR__ . '/../partials/season_select.php'; ?>
                        </select>
                    </div>
                </div>

                <div class="pt-6 border-t border-border">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-text-main">Scheduling Defaults</h3>
                        <p class="text-sm text-text-muted">These settings will be used to automatically schedule the first round.</p>
                    </div>
                    <?php
                    $required = false;
                    $entity = ['startDate' => date('Y-m-d'), 'matchTime' => '15:00'];
                    include __DIR__ . '/../partials/scheduling_fields.php';
                    ?>
                </div>

                <div class="pt-6 border-t border-border">
                    <?php
                    $label = 'Select Teams';
                    $description = 'Select the teams to include. Teams will be randomly drawn into the bracket.';
                    $showCount = true;
                    include __DIR__ . '/../partials/team_selector.php';
                    ?>
                </div>

                <div class="flex items-center gap-4 pt-6 border-t border-border">
                    <button type="submit" class="btn btn-primary">Create Cup</button>
                    <a href="<?=$basePath?>/admin/cups" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>