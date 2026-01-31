<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold m-0">Add Team</h1>
        <a href="/admin/teams" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="card">
        <form method="POST" action="/admin/teams/store">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-text-muted mb-2">Team Name <span
                        class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-input" required autofocus
                    placeholder="e.g. Red Lions FC">
            </div>

            <div class="mb-6">
                <label for="contact" class="block text-sm font-medium text-text-muted mb-2">Contact Person</label>
                <input type="text" id="contact" name="contact" class="form-input" placeholder="e.g. John Smith">
                <p class="mt-2 text-sm text-text-muted">The main person to get in touch with about this team.</p>
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-text-muted mb-2">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="e.g. john@example.com">
            </div>

            <div class="mb-6">
                <label for="colour" class="block text-sm font-medium text-text-muted mb-2">Team Colour</label>
                <div class="flex items-center gap-4">
                    <input type="color" id="colour" name="colour" value="#1a5f2a"
                        class="h-10 w-20 p-1 bg-background border border-border rounded cursor-pointer">
                    <span class="colour-value font-mono text-text-muted">#1a5f2a</span>
                </div>
                <p class="mt-2 text-sm text-text-muted">Used to identify this team in fixtures and tables.</p>
            </div>

            <div class="mb-8">
                <label for="players" class="block text-sm font-medium text-text-muted mb-2">Players</label>
                <textarea id="players" name="players" class="form-input min-h-[150px]"
                    placeholder="James Wilson&#10;David Brown&#10;Michael Taylor"></textarea>
                <p class="mt-2 text-sm text-text-muted">Enter one player name per line.</p>
            </div>

            <div class="flex items-center gap-4 pt-6 border-t border-border">
                <button type="submit" class="btn btn-primary">Create Team</button>
                <a href="/admin/teams" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        // Update colour value display when picker changes
        document.getElementById('colour').addEventListener('input', function (e) {
            document.querySelector('.colour-value').textContent = e.target.value;
        });
    </script>
</div>