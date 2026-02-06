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
                <?= date('l, j F Y', strtotime($fixture['matchDate'])) ?>
                <?php if ($fixture['matchTime']): ?>
                    • <?= date('g:i A', strtotime($fixture['matchTime'])) ?>
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
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-6">
                    <?php foreach ($photos as $photo): ?>
                        <div class="bg-surface-hover border border-border rounded-lg p-3">
                            <!-- Thumbnail -->
                            <img src="<?= $basePath ?>/uploads/<?= htmlspecialchars($photo['filePath']) ?>"
                                 alt="Match photo"
                                 class="w-full h-32 object-cover rounded mb-2"
                                 id="photo-<?= $photo['id'] ?>">

                            <!-- Delete Button -->
                            <form method="POST"
                                  action="<?= $basePath ?>/admin/fixture/<?= $fixtureType ?>/<?= $competition['slug'] ?>/<?= $fixtureSlug ?>/photos/<?= $photo['id'] ?>/delete"
                                  onsubmit="return confirm('Delete this photo?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit"
                                        class="w-full bg-danger hover:bg-danger-hover text-white py-1 px-2 rounded text-sm font-medium transition-colors"
                                        title="Delete photo">Delete</button>
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
                    <div id="drop-zone" class="border-2 border-dashed border-border rounded-lg p-8 text-center bg-surface-hover/50 hover:border-primary transition-colors cursor-pointer">
                        <input type="file"
                               id="file-input"
                               name="photos[]"
                               multiple
                               accept="image/jpeg,image/png,image/webp"
                               class="hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-lg font-semibold text-text-main mb-1">Drop photos here or click to browse</p>
                        <p class="text-sm text-text-muted">
                            JPG, PNG, or WebP (max 5MB each)
                        </p>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    // Prevent drag/drop from opening files in browser, but allow our drop zone to work
    const dropZoneId = 'drop-zone';

    ['dragover', 'drop'].forEach(function(event) {
        window.addEventListener(event, function(e) {
            // Only prevent default if NOT dropping on our drop zone
            if (e.target.id !== dropZoneId && !document.getElementById(dropZoneId).contains(e.target)) {
                e.preventDefault();
            }
        }, true);
    });

    // NOW set up drop zone functionality
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');

    // Click to browse
    dropZone.addEventListener('click', function(e) {
        fileInput.click();
    });

    // Allow drag events on the drop zone specifically
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('border-primary', 'bg-primary/10');
    });

    dropZone.addEventListener('dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('border-primary', 'bg-primary/10');
    });

    dropZone.addEventListener('dragleave', function(e) {
        dropZone.classList.remove('border-primary', 'bg-primary/10');
    });

    // Handle dropped files on the drop zone
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();

        dropZone.classList.remove('border-primary', 'bg-primary/10');

        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            // Store the dropped files
            handleFiles(e.dataTransfer.files);
        }
    });

    // Handle photo file selection and auto-upload
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    function uploadFiles(files) {
        const uploadForm = document.querySelector('form[enctype="multipart/form-data"]');
        const dropZone = document.getElementById('drop-zone');

        // Show uploading state
        dropZone.innerHTML = '<p class="text-lg font-semibold text-primary">Uploading ' + files.length + ' photo(s)...</p>';

        // Create FormData and add files manually
        const formData = new FormData();
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

        // Add files
        Array.from(files).forEach((file) => {
            formData.append('photos[]', file);
        });

        // Add empty captions for now
        Array.from(files).forEach(() => {
            formData.append('captions[]', '');
        });

        // Submit via fetch
        fetch(uploadForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Success if ok (200-299) or redirect (302)
            if (response.ok || response.redirected || response.status === 302) {
                window.location.reload();
            } else {
                return response.text().then(text => {
                    console.error('Upload failed with status:', response.status);
                    console.error('Response:', text);
                    dropZone.innerHTML = '<p class="text-lg font-semibold text-red-500">Upload failed (status ' + response.status + '). Check console for details.</p>';
                });
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            dropZone.innerHTML = '<p class="text-lg font-semibold text-red-500">Upload failed: ' + error.message + '</p>';
        });
    }

    function handleFiles(files) {
        if (files.length > 0) {
            // Auto-upload immediately
            uploadFiles(files);
        }
    }

</script>
