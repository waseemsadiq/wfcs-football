<div class="">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold m-0">Add Team</h1>
        <a href="<?= $basePath ?>/admin/teams" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="card">
        <form method="POST" action="<?= $basePath ?>/admin/teams/store">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-text-muted mb-2">Team Name <span
                        class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-input" required aria-required="true" autofocus
                    placeholder="e.g. Red Lions FC">
            </div>

            <div class="mb-6">
                <label for="contact" class="block text-sm font-medium text-text-muted mb-2">Contact Person</label>
                <input type="text" id="contact" name="contact" class="form-input" placeholder="e.g. John Smith">
                <p class="mt-2 text-sm text-text-muted">The main person to get in touch with about this team.</p>
            </div>

            <div class="mb-6">
                <label for="phone" class="block text-sm font-medium text-text-muted mb-2">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-input" placeholder="e.g. 07700 900123">
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-text-muted mb-2">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="e.g. john@example.com">
            </div>

            <?php
            $colourValue = '#1a5f2a';
            include __DIR__ . '/../partials/colour_picker.php';
            ?>

            <div class="mb-8 p-4 bg-blue-500/10 border border-blue-500/30 rounded">
                <p class="text-sm text-blue-400 mb-2"><strong>Note:</strong> Players are now managed separately.</p>
                <p class="text-sm text-text-muted">After creating the team, use the <strong class="text-text-main">Manage Squad</strong> button to add players.</p>
            </div>

            <div class="flex items-center gap-4 pt-6 border-t border-border">
                <button type="submit" class="btn btn-primary">Create Team</button>
                <a href="<?= $basePath ?>/admin/teams" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>