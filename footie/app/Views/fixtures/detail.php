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

        <!-- Status and Referee -->
        <div class="card mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status -->
                    <div>
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

                    <!-- Referee -->
                    <div>
                        <label for="referee_id" class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                            Referee
                        </label>
                        <select name="referee_id" id="referee_id"
                                class="w-full bg-surface border border-border text-text-main rounded-sm px-4 py-3 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">No referee assigned</option>
                            <?php foreach ($referees as $referee): ?>
                                <option value="<?= htmlspecialchars($referee['id']) ?>"
                                    <?= ($fixture['refereeId'] ?? null) == $referee['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($referee['name']) ?>
                                    <?php if (isset($referee['teamName'])): ?>
                                        (<?= htmlspecialchars($referee['teamName']) ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-sm text-text-muted mt-2">
                            Select the match referee
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Match Report -->
        <div class="card mb-6">
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
        <div class="card mb-6">
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
                           placeholder="https://youtu.be/...">
                    <p class="text-sm text-text-muted mt-2">
                        Cloudflare Stream, YouTube, or Vimeo URL (shown when status is "In Progress"). Supports shortened and browser URLs.
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
                           placeholder="https://www.youtube.com/watch?v=...">
                    <p class="text-sm text-text-muted mt-2">
                        Full match replay URL (takes priority over highlights when available). Supports all major video platforms.
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
                           placeholder="https://vimeo.com/...">
                    <p class="text-sm text-text-muted mt-2">
                        Match highlights URL (shown when full match URL is not set).
                    </p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card mb-6">
            <div class="p-6">
                <div class="flex items-center gap-4">
                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>
                    <a href="<?= $basePath ?>/admin/<?= $fixtureType ?>s/<?= $competition['slug'] ?>/fixtures"
                       class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Photo Gallery -->
    <div class="card mb-6">
        <div class="p-6">
            <h3 class="text-lg font-bold text-text-main mb-4">Match Photos</h3>

            <!-- Existing Photos -->
            <?php if (!empty($photos)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <?php foreach ($photos as $photo): ?>
                        <div class="relative group">
                            <img src="<?= $basePath ?>/uploads/fixtures/<?= htmlspecialchars($photo['filePath']) ?>"
                                 alt="<?= htmlspecialchars($photo['caption'] ?? 'Match photo') ?>"
                                 class="w-full aspect-video object-cover rounded-lg border border-border transition-opacity"
                                 id="photo-<?= $photo['id'] ?>">
                            <?php if ($photo['caption']): ?>
                                <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/90 to-transparent p-3 rounded-b-lg">
                                    <p class="text-sm text-white font-medium"><?= htmlspecialchars($photo['caption']) ?></p>
                                </div>
                            <?php endif; ?>
                            <!-- Delete Button -->
                            <form method="POST"
                                  action="<?= $basePath ?>/admin/fixture/<?= $fixtureType ?>/<?= $competition['slug'] ?>/<?= $fixtureSlug ?>/photos/<?= $photo['id'] ?>/delete"
                                  class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity"
                                  onsubmit="return confirm('Delete this photo?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Upload Form -->
            <form method="POST"
                  action="<?= $basePath ?>/admin/fixture/<?= $fixtureType ?>/<?= $competition['slug'] ?>/<?= $fixtureSlug ?>/photos"
                  enctype="multipart/form-data"
                  class="border-2 border-dashed border-border rounded-lg p-6">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="mb-4">
                    <label class="block text-sm font-bold text-text-muted uppercase tracking-wider mb-3">
                        Upload Photos
                    </label>
                    <input type="file"
                           name="photos[]"
                           multiple
                           accept="image/jpeg,image/png,image/webp"
                           class="w-full text-text-main file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-hover file:cursor-pointer">
                    <p class="text-sm text-text-muted mt-2">
                        Select one or more photos (JPG, PNG, or WebP, max 5MB each)
                    </p>
                </div>

                <div id="caption-inputs" class="space-y-2 mb-4 hidden">
                    <!-- Caption inputs will be added here dynamically -->
                </div>

                <button type="submit" class="btn btn-primary">
                    Upload Photos
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Handle photo file selection and generate caption inputs
    document.querySelector('input[name="photos[]"]').addEventListener('change', function(e) {
        const captionInputs = document.getElementById('caption-inputs');
        const files = e.target.files;

        // Clear existing caption inputs
        captionInputs.innerHTML = '';

        if (files.length > 0) {
            captionInputs.classList.remove('hidden');

            // Create caption input for each file
            Array.from(files).forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2';

                const fileNameSpan = document.createElement('span');
                fileNameSpan.className = 'text-sm text-text-muted font-medium w-40 truncate';
                fileNameSpan.textContent = file.name;

                const captionInput = document.createElement('input');
                captionInput.type = 'text';
                captionInput.name = 'captions[]';
                captionInput.placeholder = 'Caption (optional)';
                captionInput.className = 'flex-1 bg-surface border border-border text-text-main rounded-sm px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary';

                div.appendChild(fileNameSpan);
                div.appendChild(captionInput);
                captionInputs.appendChild(div);
            });
        } else {
            captionInputs.classList.add('hidden');
        }
    });
</script>
