<div class="">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold m-0">Edit <?= htmlspecialchars($team['name'] ?? '') ?></h1>
        <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id'] ?? '') ?>"
            class="btn btn-secondary">Back to Team</a>
    </div>

    <!-- Tab Navigation -->
    <div class="card mb-6">
        <nav class="flex border-b border-border">
            <button type="button" data-tab="details"
                class="tab-button px-6 py-4 font-semibold text-sm uppercase tracking-wide transition-colors border-b-2 border-primary text-primary">
                Team Details
            </button>
            <button type="button" data-tab="squad"
                class="tab-button px-6 py-4 font-semibold text-sm uppercase tracking-wide transition-colors border-b-2 border-transparent text-text-muted hover:text-primary">
                Squad
            </button>
            <button type="button" data-tab="staff"
                class="tab-button px-6 py-4 font-semibold text-sm uppercase tracking-wide transition-colors border-b-2 border-transparent text-text-muted hover:text-primary">
                Support Staff
            </button>
        </nav>
    </div>

    <!-- Tab: Team Details -->
    <div id="tab-details" class="tab-content">
        <div class="card">
            <form method="POST"
                action="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id'] ?? '') ?>/update">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-text-muted mb-2">Team Name <span
                            class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-input" required aria-required="true"
                        value="<?= htmlspecialchars($team['name'] ?? '') ?>">
                </div>

                <div class="mb-6">
                    <label for="contact" class="block text-sm font-medium text-text-muted mb-2">Contact Person</label>
                    <input type="text" id="contact" name="contact" class="form-input"
                        value="<?= htmlspecialchars($team['contact'] ?? '') ?>" placeholder="e.g. John Smith">
                    <p class="mt-2 text-sm text-text-muted">The main person to get in touch with about this team.</p>
                </div>

                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-text-muted mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-input"
                        value="<?= htmlspecialchars($team['phone'] ?? '') ?>" placeholder="e.g. 07700 900123">
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

                <div class="flex items-center gap-4 pt-6 border-t border-border">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="<?= $basePath ?>/admin/teams/<?= htmlspecialchars($team['slug'] ?? $team['id'] ?? '') ?>"
                        class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab: Squad -->
    <div id="tab-squad" class="tab-content hidden">
        <div class="card">
            <h2 class="text-xl font-bold mb-6 pb-4 border-b border-border">Squad Management</h2>

            <?php
            $playerModel = new \App\Models\Player();
            $squadPlayers = $playerModel->getByTeam($team['id']);
            ?>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-text-muted">Manage players, positions, squad numbers, and status.</p>
                    <span class="px-3 py-1 bg-surface-hover rounded-full text-xs font-bold text-text-muted uppercase tracking-wider">
                        <?= count($squadPlayers) ?> player<?= count($squadPlayers) !== 1 ? 's' : '' ?>
                    </span>
                </div>

                <div class="flex gap-4">
                    <a href="<?= $basePath ?>/admin/players?team_id=<?= htmlspecialchars($team['id']) ?>"
                        class="btn btn-primary">Manage Squad →</a>
                    <a href="<?= $basePath ?>/admin/players/create?team_id=<?= htmlspecialchars($team['id']) ?>"
                        class="btn btn-secondary">+ Add Player</a>
                </div>
            </div>

            <?php if (!empty($squadPlayers)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="table-th text-left">Name</th>
                                <th class="table-th text-left">Position</th>
                                <th class="table-th text-center">#</th>
                                <th class="table-th text-left">Status</th>
                                <th class="table-th text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($squadPlayers as $player): ?>
                                <tr class="hover:bg-surface-hover transition-colors">
                                    <td class="table-td">
                                        <a href="<?= $basePath ?>/admin/players/<?= htmlspecialchars($player['slug']) ?>"
                                            class="font-medium hover:text-primary transition-colors">
                                            <?= htmlspecialchars($player['name']) ?>
                                        </a>
                                    </td>
                                    <td class="table-td">
                                        <?php if (!empty($player['position'])): ?>
                                            <span class="text-xs px-2 py-1 rounded bg-surface-hover">
                                                <?= htmlspecialchars($player['position']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-text-muted italic">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-td text-center">
                                        <?php if (!empty($player['squadNumber'])): ?>
                                            <span class="font-mono font-bold"><?= htmlspecialchars($player['squadNumber']) ?></span>
                                        <?php else: ?>
                                            <span class="text-text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-td">
                                        <?php
                                        $statusColors = [
                                            'active' => 'bg-green-500/20 text-green-400',
                                            'injured' => 'bg-red-500/20 text-red-400',
                                            'suspended' => 'bg-yellow-500/20 text-yellow-400',
                                            'unavailable' => 'bg-gray-500/20 text-gray-400',
                                        ];
                                        $statusColor = $statusColors[$player['status']] ?? 'bg-gray-500/20 text-gray-400';
                                        ?>
                                        <span class="text-xs px-2 py-1 rounded <?= $statusColor ?>">
                                            <?= htmlspecialchars(ucfirst($player['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="table-td text-right">
                                        <a href="<?= $basePath ?>/admin/players/<?= htmlspecialchars($player['slug']) ?>/edit"
                                            class="btn btn-secondary btn-sm">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12 text-text-muted bg-surface-hover/30 rounded border border-border">
                    <p class="mb-4">No players added to this team yet.</p>
                    <a href="<?= $basePath ?>/admin/players/create?team_id=<?= htmlspecialchars($team['id']) ?>"
                        class="btn btn-primary">Add First Player</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab: Support Staff -->
    <div id="tab-staff" class="tab-content hidden">
        <div class="card">
            <h2 class="text-xl font-bold mb-6 pb-4 border-b border-border">Support Staff</h2>

            <?php
            $staffModel = new \App\Models\TeamStaff();
            $teamStaff = $staffModel->getByTeam($team['id']);
            ?>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-text-muted">Manage coaches, managers, referees, and contacts.</p>
                    <span class="px-3 py-1 bg-surface-hover rounded-full text-xs font-bold text-text-muted uppercase tracking-wider">
                        <?= count($teamStaff) ?> staff member<?= count($teamStaff) !== 1 ? 's' : '' ?>
                    </span>
                </div>

                <div class="flex gap-4">
                    <a href="<?= $basePath ?>/admin/staff?team_id=<?= htmlspecialchars($team['id']) ?>"
                        class="btn btn-primary">Manage Staff →</a>
                    <a href="<?= $basePath ?>/admin/staff/create?team_id=<?= htmlspecialchars($team['id']) ?>"
                        class="btn btn-secondary">+ Add Staff Member</a>
                </div>
            </div>

            <?php if (!empty($teamStaff)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="table-th text-left">Name</th>
                                <th class="table-th text-left">Role</th>
                                <th class="table-th text-left">Contact</th>
                                <th class="table-th text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teamStaff as $member): ?>
                                <tr class="hover:bg-surface-hover transition-colors">
                                    <td class="table-td">
                                        <a href="<?= $basePath ?>/admin/staff/<?= htmlspecialchars($member['id']) ?>"
                                            class="font-medium hover:text-primary transition-colors">
                                            <?= htmlspecialchars($member['name']) ?>
                                        </a>
                                    </td>
                                    <td class="table-td">
                                        <span class="inline-block px-2 py-1 text-xs rounded bg-primary/10 text-primary">
                                            <?= htmlspecialchars(\App\Models\TeamStaff::formatRole($member['role'])) ?>
                                        </span>
                                    </td>
                                    <td class="table-td">
                                        <?php if (!empty($member['email'])): ?>
                                            <a href="mailto:<?= htmlspecialchars($member['email']) ?>"
                                                class="text-primary hover:underline">
                                                <?= htmlspecialchars($member['email']) ?>
                                            </a>
                                        <?php elseif (!empty($member['phone'])): ?>
                                            <a href="tel:<?= htmlspecialchars($member['phone']) ?>"
                                                class="text-primary hover:underline">
                                                <?= htmlspecialchars($member['phone']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-text-muted italic">Not set</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-td text-right">
                                        <a href="<?= $basePath ?>/admin/staff/<?= htmlspecialchars($member['id']) ?>/edit"
                                            class="btn btn-secondary btn-sm">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12 text-text-muted bg-surface-hover/30 rounded border border-border">
                    <p class="mb-4">No support staff added to this team yet.</p>
                    <a href="<?= $basePath ?>/admin/staff/create?team_id=<?= htmlspecialchars($team['id']) ?>"
                        class="btn btn-primary">Add First Staff Member</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.dataset.tab;

                // Update button states
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-primary', 'text-primary');
                    btn.classList.add('border-transparent', 'text-text-muted');
                });
                this.classList.remove('border-transparent', 'text-text-muted');
                this.classList.add('border-primary', 'text-primary');

                // Update tab content visibility
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(`tab-${targetTab}`).classList.remove('hidden');
            });
        });
    </script>
</div>
