<?php
/**
 * Admin Fixture Detail Editor
 * Edit match report, status, and media URLs
 */
?>

<div class="max-w-4xl mx-auto">
    <?php
    $title = 'Edit Fixture: ' . $fixture['homeTeamName'] . ' vs ' . $fixture['awayTeamName'];
    $subtitle = $competition['name'];
    include __DIR__ . '/../partials/page_header.php';
    ?>

    <!-- Quick Links -->
    <div class="card mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <a href="<?= $basePath ?>/fixture/<?= $fixtureType ?>/<?= $competition['slug'] ?>/<?= $fixtureSlug ?>"
                   target="_blank"
                   class="btn btn-secondary">
                    View Public Page →
                </a>
                <a href="<?= $basePath ?>/admin/<?= $fixtureType ?>s/<?= $competition['slug'] ?>/fixtures"
                   class="text-text-muted hover:text-primary transition-colors">
                    ← Back to Fixtures
                </a>
            </div>
        </div>
    </div>

    <!-- Match Info Card -->
    <div class="card mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <div>
                        <span class="text-2xl font-extrabold text-text-main">
                            <?= htmlspecialchars($fixture['homeTeamName']) ?>
                        </span>
                        <?php if ($fixture['result']): ?>
                            <span class="text-3xl font-bold text-primary mx-3">
                                <?= $fixture['result']['homeScore'] ?> - <?= $fixture['result']['awayScore'] ?>
                            </span>
                        <?php else: ?>
                            <span class="text-2xl text-text-muted mx-3">vs</span>
                        <?php endif; ?>
                        <span class="text-2xl font-extrabold text-text-main">
                            <?= htmlspecialchars($fixture['awayTeamName']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-sm text-text-muted">
                <?= date('l, j F Y', strtotime($fixture['date'])) ?>
                <?php if ($fixture['time']): ?>
                    • <?= date('g:i A', strtotime($fixture['time'])) ?>
                <?php endif; ?>
                <?php if ($fixture['pitch']): ?>
                    • Pitch <?= htmlspecialchars($fixture['pitch']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="<?= $basePath ?>/admin/fixture/<?= $fixtureType ?>/<?= $competition['slug'] ?>/<?= $fixtureSlug ?>" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <!-- Status -->
        <div class="card">
            <div class="p-6">
                <label for="status" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                    Match Status
                </label>
                <select name="status" id="status"
                        class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="scheduled" <?= ($fixture['status'] ?? 'scheduled') === 'scheduled' ? 'selected' : '' ?>>
                        Scheduled
                    </option>
                    <option value="in_progress" <?= ($fixture['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>
                        In Progress (Live)
                    </option>
                    <option value="completed" <?= ($fixture['status'] ?? '') === 'completed' ? 'selected' : '' ?>>
                        Completed
                    </option>
                    <option value="postponed" <?= ($fixture['status'] ?? '') === 'postponed' ? 'selected' : '' ?>>
                        Postponed
                    </option>
                    <option value="cancelled" <?= ($fixture['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>
                        Cancelled
                    </option>
                </select>
                <p class="text-sm text-text-muted mt-2">
                    Set to "In Progress" to show live stream, "Completed" to show full match/highlights
                </p>
            </div>
        </div>

        <!-- Match Report -->
        <div class="card">
            <div class="p-6">
                <label for="match_report" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                    Match Report
                </label>
                <textarea name="match_report" id="match_report" rows="8"
                          class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-mono text-sm"
                          placeholder="Write a match report (optional)..."><?= htmlspecialchars($fixture['matchReport'] ?? '') ?></textarea>
                <p class="text-sm text-text-muted mt-2">
                    Prose description of the match - key moments, standout performances, etc.
                </p>
            </div>
        </div>

        <!-- Video URLs -->
        <div class="card">
            <div class="p-6">
                <h3 class="text-lg font-bold text-text-main mb-4">Video Embeds</h3>

                <!-- Live Stream URL -->
                <div class="mb-4">
                    <label for="live_stream_url" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                        Live Stream URL
                    </label>
                    <input type="url" name="live_stream_url" id="live_stream_url"
                           value="<?= htmlspecialchars($fixture['liveStreamUrl'] ?? '') ?>"
                           class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="https://stream.cloudflare.com/...">
                    <p class="text-sm text-text-muted mt-2">
                        Cloudflare Stream, YouTube, or Vimeo embed URL (shown when status is "In Progress")
                    </p>
                </div>

                <!-- Full Match URL -->
                <div class="mb-4">
                    <label for="full_match_url" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                        Full Match URL
                    </label>
                    <input type="url" name="full_match_url" id="full_match_url"
                           value="<?= htmlspecialchars($fixture['fullMatchUrl'] ?? '') ?>"
                           class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="https://www.youtube.com/embed/...">
                    <p class="text-sm text-text-muted mt-2">
                        Full match replay embed URL (takes priority over highlights when available)
                    </p>
                </div>

                <!-- Highlights URL -->
                <div class="mb-4">
                    <label for="highlights_url" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                        Highlights URL
                    </label>
                    <input type="url" name="highlights_url" id="highlights_url"
                           value="<?= htmlspecialchars($fixture['highlightsUrl'] ?? '') ?>"
                           class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="https://www.youtube.com/embed/...">
                    <p class="text-sm text-text-muted mt-2">
                        Match highlights embed URL (shown when full match URL is not set)
                    </p>
                </div>
            </div>
        </div>

        <!-- Photo Gallery (Future Enhancement) -->
        <div class="card">
            <div class="p-6">
                <h3 class="text-lg font-bold text-text-main mb-4">Match Photos</h3>
                <div class="bg-surface-hover/30 border-2 border-dashed border-border rounded-lg p-8 text-center">
                    <p class="text-text-muted mb-2">Photo upload coming soon</p>
                    <p class="text-sm text-text-muted">
                        Will support drag-and-drop image uploads for match galleries
                    </p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-primary">
                Save Changes
            </button>
            <a href="<?= $basePath ?>/admin/<?= $fixtureType ?>s/<?= $competition['slug'] ?>/fixtures"
               class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>
