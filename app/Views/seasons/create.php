<div class="">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold m-0">Create Season</h1>
        <a href="<?=$basePath?>/admin/seasons" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="card">
        <form method="POST" action="<?=$basePath?>/admin/seasons/store">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-6">
                <label for="id" class="block text-sm font-medium text-text-muted mb-2">Season ID</label>
                <input type="text" id="id" name="id" class="form-input" required placeholder="e.g. 2024-25"
                    pattern="[a-zA-Z0-9\-]+" title="Use letters, numbers, and hyphens only">
                <p class="mt-2 text-sm text-text-muted">A unique identifier for this season (letters, numbers, and
                    hyphens only).</p>
            </div>

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-text-muted mb-2">Season Name</label>
                <input type="text" id="name" name="name" class="form-input" required placeholder="e.g. 2024/25 Season">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="startDate" class="block text-sm font-medium text-text-muted mb-2">Start Date</label>
                    <input type="date" id="startDate" name="startDate" class="form-input" required>
                </div>
                <div>
                    <label for="endDate" class="block text-sm font-medium text-text-muted mb-2">End Date</label>
                    <input type="date" id="endDate" name="endDate" class="form-input" required>
                </div>
            </div>

            <div class="mb-8">
                <label
                    class="flex items-center gap-3 p-3 bg-surface-hover/50 rounded-sm cursor-pointer border border-transparent hover:border-primary/50 transition-colors">
                    <input type="checkbox" name="isActive" value="1" class="w-4 h-4 accent-primary cursor-pointer">
                    <span class="font-medium text-text-main">Set as active season</span>
                </label>
                <p class="mt-2 text-sm text-text-muted ml-1">The active season is shown on the dashboard and used as the
                    default when creating leagues and cups.</p>
            </div>

            <div class="flex items-center gap-4 pt-6 border-t border-border">
                <button type="submit" class="btn btn-primary">Create Season</button>
                <a href="<?=$basePath?>/admin/seasons" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>