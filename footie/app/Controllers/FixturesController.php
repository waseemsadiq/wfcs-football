<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;

/**
 * FixturesController - Admin fixture detail management.
 * Handles both league and cup fixtures.
 */
class FixturesController extends Controller
{
    /**
     * Display admin fixture detail editor.
     */
    public function fixtureDetail(string $type, string $competitionSlug, string $fixtureSlug): void
    {
        // Validate fixture type
        if (!in_array($type, ['league', 'cup'])) {
            $this->redirect('/admin');
            return;
        }

        // Parse fixture slug (format: "home-team-slug-vs-away-team-slug")
        if (!preg_match('/^(.+)-vs-(.+)$/', $fixtureSlug, $matches)) {
            $this->redirect('/admin');
            return;
        }

        $homeTeamSlug = $matches[1];
        $awayTeamSlug = $matches[2];

        // Load teams to convert slugs to IDs
        $teamModel = new \App\Models\Team();
        $homeTeam = $teamModel->findWhere('slug', $homeTeamSlug);
        $awayTeam = $teamModel->findWhere('slug', $awayTeamSlug);

        if (!$homeTeam || !$awayTeam) {
            $this->redirect('/admin');
            return;
        }

        // Load competition and find fixture
        if ($type === 'league') {
            $leagueModel = new \App\Models\League();
            $competition = $leagueModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->redirect('/admin/leagues');
                return;
            }

            $fixture = $this->findFixtureByTeamIds(
                $competition['fixtures'],
                $homeTeam['id'],
                $awayTeam['id']
            );

            if ($fixture) {
                $fixtureDetail = $leagueModel->getFixtureWithDetails($fixture['id']);
            }
        } else {
            $cupModel = new \App\Models\Cup();
            $competition = $cupModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->redirect('/admin/cups');
                return;
            }

            $fixture = $this->findFixtureInRounds(
                $competition['rounds'],
                $homeTeam['id'],
                $awayTeam['id']
            );

            if ($fixture) {
                $fixtureDetail = $cupModel->getFixtureWithDetails($fixture['id']);
            }
        }

        if (!isset($fixtureDetail) || !$fixtureDetail) {
            $this->redirect('/admin');
            return;
        }

        // Load photos for this fixture
        $photoModel = new \App\Models\FixturePhoto();
        $photos = $photoModel->getByFixture($type, $fixtureDetail['id']);

        // Load referees for dropdown
        $staffModel = new \App\Models\TeamStaff();
        $referees = $staffModel->getByRole('referee');
        usort($referees, fn($a, $b) => strcmp($a['name'], $b['name']));

        $this->render('fixtures/detail', [
            'title' => 'Edit Fixture: ' . $fixtureDetail['homeTeamName'] . ' vs ' . $fixtureDetail['awayTeamName'],
            'fixtureType' => $type,
            'competition' => $competition,
            'fixture' => $fixtureDetail,
            'fixtureSlug' => $fixtureSlug,
            'photos' => $photos,
            'referees' => $referees,
        ]);
    }

    /**
     * Update fixture rich content details.
     */
    public function updateFixtureDetail(string $type, string $competitionSlug, string $fixtureSlug): void
    {
        // Validate fixture type
        if (!in_array($type, ['league', 'cup'])) {
            $this->flash('error', 'Invalid fixture type');
            $this->redirect('/admin');
            return;
        }

        // Parse fixture slug
        if (!preg_match('/^(.+)-vs-(.+)$/', $fixtureSlug, $matches)) {
            $this->flash('error', 'Invalid fixture');
            $this->redirect('/admin');
            return;
        }

        $homeTeamSlug = $matches[1];
        $awayTeamSlug = $matches[2];

        // Load teams to convert slugs to IDs
        $teamModel = new \App\Models\Team();
        $homeTeam = $teamModel->findWhere('slug', $homeTeamSlug);
        $awayTeam = $teamModel->findWhere('slug', $awayTeamSlug);

        if (!$homeTeam || !$awayTeam) {
            $this->flash('error', 'Teams not found');
            $this->redirect('/admin');
            return;
        }

        // Load competition and find fixture
        if ($type === 'league') {
            $leagueModel = new \App\Models\League();
            $competition = $leagueModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->flash('error', 'Competition not found');
                $this->redirect('/admin/leagues');
                return;
            }

            $fixture = $this->findFixtureByTeamIds(
                $competition['fixtures'],
                $homeTeam['id'],
                $awayTeam['id']
            );
        } else {
            $cupModel = new \App\Models\Cup();
            $competition = $cupModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->flash('error', 'Competition not found');
                $this->redirect('/admin/cups');
                return;
            }

            $fixture = $this->findFixtureInRounds(
                $competition['rounds'],
                $homeTeam['id'],
                $awayTeam['id']
            );
        }

        if (!$fixture) {
            $this->flash('error', 'Fixture not found');
            $this->redirect('/admin');
            return;
        }

        // Get form data
        $details = [
            'status' => $this->post('status'),
            'refereeId' => $this->post('referee_id'),
            'matchReport' => $this->post('match_report'),
            'liveStreamUrl' => $this->post('live_stream_url'),
            'fullMatchUrl' => $this->post('full_match_url'),
            'highlightsUrl' => $this->post('highlights_url'),
        ];

        // Update fixture rich content
        if ($type === 'league') {
            $leagueModel->updateFixtureRichContent($fixture['id'], $details);
        } else {
            $cupModel->updateFixtureRichContent($fixture['id'], $details);
        }

        $this->flash('success', 'Fixture details updated successfully');
        $this->redirect("/admin/fixture/{$type}/{$competitionSlug}/{$fixtureSlug}");
    }

    /**
     * Find fixture by team IDs in a fixture list.
     */
    private function findFixtureByTeamIds(array $fixtures, int $homeId, int $awayId): ?array
    {
        $matches = [];
        foreach ($fixtures as $fixture) {
            if (isset($fixture['homeTeamId'], $fixture['awayTeamId'])) {
                if ($fixture['homeTeamId'] == $homeId && $fixture['awayTeamId'] == $awayId) {
                    $matches[] = $fixture;
                }
            }
        }

        if (empty($matches)) {
            return null;
        }

        // 1. Prefer completed matches (those with results/scores)
        foreach ($matches as $match) {
            if (isset($match['result']) && $match['result'] !== null) {
                return $match;
            }
        }

        // 2. Prefer matches with videos (active or historical content)
        foreach ($matches as $match) {
            if (!empty($match['fullMatchUrl']) || !empty($match['highlightsUrl']) || !empty($match['liveStreamUrl'])) {
                return $match;
            }
        }

        // 3. Default to the most recent one (assuming order from model)
        return $matches[count($matches) - 1];
    }

    /**
     * Find fixture by team IDs in cup rounds.
     */
    private function findFixtureInRounds(array $rounds, int $homeId, int $awayId): ?array
    {
        foreach ($rounds as $round) {
            foreach ($round['fixtures'] as $fixture) {
                if (isset($fixture['homeTeamId'], $fixture['awayTeamId'])) {
                    if (
                        $fixture['homeTeamId'] == $homeId &&
                        $fixture['awayTeamId'] == $awayId
                    ) {
                        return $fixture;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Upload photos for a fixture.
     */
    public function uploadPhotos(string $type, string $competitionSlug, string $fixtureSlug): void
    {
        try {
            error_log("=== Upload Photos Start ===");
            error_log("Type: $type, Competition: $competitionSlug, Fixture: $fixtureSlug");

            // Validate fixture type
            if (!in_array($type, ['league', 'cup'])) {
                $this->flash('error', 'Invalid fixture type');
                $this->redirect('/admin');
                return;
            }

        // Parse fixture slug
        if (!preg_match('/^(.+)-vs-(.+)$/', $fixtureSlug, $matches)) {
            error_log("Invalid fixture slug format");
            $this->flash('error', 'Invalid fixture');
            $this->redirect('/admin');
            return;
        }

        $homeTeamSlug = $matches[1];
        $awayTeamSlug = $matches[2];
        error_log("Team slugs: home=$homeTeamSlug, away=$awayTeamSlug");

        // Load teams
        $teamModel = new \App\Models\Team();
        $homeTeam = $teamModel->findWhere('slug', $homeTeamSlug);
        $awayTeam = $teamModel->findWhere('slug', $awayTeamSlug);

        if (!$homeTeam || !$awayTeam) {
            error_log("Teams not found: home=" . ($homeTeam ? 'found' : 'NOT FOUND') . ", away=" . ($awayTeam ? 'found' : 'NOT FOUND'));
            $this->flash('error', 'Teams not found');
            $this->redirect('/admin');
            return;
        }
        error_log("Teams found: home_id={$homeTeam['id']}, away_id={$awayTeam['id']}");

        // Load competition and find fixture
        if ($type === 'league') {
            $leagueModel = new \App\Models\League();
            $competition = $leagueModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                error_log("League competition not found");
                $this->flash('error', 'Competition not found');
                $this->redirect('/admin/leagues');
                return;
            }
            error_log("League competition found: id={$competition['id']}");

            $fixture = $this->findFixtureByTeamIds(
                $competition['fixtures'],
                $homeTeam['id'],
                $awayTeam['id']
            );
        } else {
            $cupModel = new \App\Models\Cup();
            $competition = $cupModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                error_log("Cup competition not found");
                $this->flash('error', 'Competition not found');
                $this->redirect('/admin/cups');
                return;
            }
            error_log("Cup competition found: id={$competition['id']}");

            $fixture = $this->findFixtureInRounds(
                $competition['rounds'],
                $homeTeam['id'],
                $awayTeam['id']
            );
        }

        if (!$fixture) {
            error_log("Fixture not found for teams {$homeTeam['id']} vs {$awayTeam['id']}");
            $this->flash('error', 'Fixture not found');
            $this->redirect('/admin');
            return;
        }
        error_log("Fixture found: id={$fixture['id']}");

        // Handle file uploads
        error_log("Checking FILES array: " . print_r($_FILES, true));

        if (!isset($_FILES['photos']) || !is_array($_FILES['photos']['name'])) {
            error_log("No files in upload");
            $this->flash('error', 'No files uploaded');
            $this->redirect("/admin/fixture/{$type}/{$competitionSlug}/{$fixtureSlug}");
            return;
        }

        $photoModel = new \App\Models\FixturePhoto();
        $uploadCount = 0;
        $errorCount = 0;
        $errors = [];

        // Get current max sort order
        $sortOrder = $photoModel->getMaxSortOrder($type, $fixture['id']);
        error_log("Current max sort order: $sortOrder");

        // Create nested directory path: fixtures/competition/fixture
        $uploadPath = 'fixtures/' . $competitionSlug . '/' . $fixtureSlug;
        error_log("Upload path: $uploadPath");

        // Process each uploaded file
        $fileCount = count($_FILES['photos']['name']);
        error_log("File count: $fileCount");

        for ($i = 0; $i < $fileCount; $i++) {
            error_log("Processing file $i");

            if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) {
                error_log("File $i has upload error: " . $_FILES['photos']['error'][$i]);
                continue;
            }

            $file = [
                'name' => $_FILES['photos']['name'][$i],
                'type' => $_FILES['photos']['type'][$i],
                'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                'error' => $_FILES['photos']['error'][$i],
                'size' => $_FILES['photos']['size'][$i],
            ];
            error_log("File details: " . print_r($file, true));

            $caption = $_POST['captions'][$i] ?? '';

            // Upload file to nested directory
            error_log("Calling Upload::uploadFile with path: $uploadPath");
            $result = \Core\Upload::uploadFile($file, $uploadPath);
            error_log("Upload result: " . print_r($result, true));

            if ($result['success']) {
                // Create database record with full relative path
                $sortOrder++;
                $fullPath = $uploadPath . '/' . $result['filename'];
                error_log("Creating photo record with path: $fullPath");

                $photoModel->create([
                    'fixture_id' => $fixture['id'],
                    'fixture_type' => $type,
                    'file_path' => $fullPath,
                    'caption' => $caption,
                    'sort_order' => $sortOrder,
                ]);
                $uploadCount++;
                error_log("Photo created successfully");
            } else {
                $errorCount++;
                $errors[] = $result['error'];
                error_log("Upload failed: " . $result['error']);
            }
        }

        error_log("Upload loop complete. Success: $uploadCount, Errors: $errorCount");

        if ($uploadCount > 0) {
            $this->flash('success', "{$uploadCount} photo(s) uploaded successfully");
        }
        if ($errorCount > 0) {
            $errorMsg = "{$errorCount} photo(s) failed to upload";
            if (!empty($errors)) {
                $errorMsg .= ": " . implode(', ', array_unique($errors));
            }
            $this->flash('error', $errorMsg);
        }
        if ($uploadCount === 0 && $errorCount === 0) {
            $this->flash('error', 'No valid files to upload');
        }

        $this->redirect("/admin/fixture/{$type}/{$competitionSlug}/{$fixtureSlug}");

        } catch (\Exception $e) {
            error_log("Upload error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->flash('error', 'Upload error: ' . $e->getMessage());
            $this->redirect("/admin/fixture/{$type}/{$competitionSlug}/{$fixtureSlug}");
        }
    }

    /**
     * Delete a fixture photo.
     */
    public function deletePhoto(string $type, string $competitionSlug, string $fixtureSlug, int $photoId): void
    {
        $photoModel = new \App\Models\FixturePhoto();

        // Delete from database and get file path
        $filePath = $photoModel->deletePhoto($photoId);

        if ($filePath) {
            // Delete physical file (filePath already includes nested path)
            $fullPath = BASE_PATH . '/uploads/' . $filePath;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            $this->flash('success', 'Photo deleted successfully');
        } else {
            $this->flash('error', 'Photo not found');
        }

        $this->redirect("/admin/fixture/{$type}/{$competitionSlug}/{$fixtureSlug}");
    }
}
