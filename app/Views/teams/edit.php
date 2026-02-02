<div class="">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold m-0">Edit <?= htmlspecialchars($team['name']) ?></h1>
        <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>"
            class="btn btn-secondary">Cancel</a>
    </div>

    <div class="card">
        <form method="POST"
            action="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>/update">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-text-muted mb-2">Team Name <span
                        class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-input" required
                    value="<?= htmlspecialchars($team['name'] ?? '') ?>">
            </div>

            <div class="mb-6">
                <label for="contact" class="block text-sm font-medium text-text-muted mb-2">Contact Person</label>
                <input type="text" id="contact" name="contact" class="form-input"
                    value="<?= htmlspecialchars($team['contact'] ?? '') ?>" placeholder="e.g. John Smith">
                <p class="mt-2 text-sm text-text-muted">The main person to get in touch with about this team.</p>
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-text-muted mb-2">Email Address</label>
                <input type="email" id="email" name="email" class="form-input"
                    value="<?= htmlspecialchars($team['email'] ?? '') ?>" placeholder="e.g. john@example.com">
            </div>

            <?php
            $colourValue = htmlspecialchars($team['colour'] ?? '#1a5f2a');
            include __DIR__ . '/../partials/colour_picker.php';
            ?>

            <div class="mb-8">
                <label for="players" class="block text-sm font-medium text-text-muted mb-2">Players</label>
                <textarea id="players" name="players" class="form-input min-h-[150px]"
                    placeholder="James Wilson&#10;David Brown&#10;Michael Taylor"><?= htmlspecialchars($playersText) ?></textarea>
                <p class="mt-2 text-sm text-text-muted">Enter one player name per line.</p>
            </div>

            <div class="flex items-center gap-4 pt-6 border-t border-border">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id']) ?>"
                    class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div></div>