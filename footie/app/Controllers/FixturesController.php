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
        $resolved = $this->resolveFixture($type, $competitionSlug, $fixtureSlug);
        if (!$resolved) {
            return;
        }

        $fixtureDetail = $resolved['model']->getFixtureWithDetails($resolved['fixture']['id']);

        if (!$fixtureDetail) {
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

        // Load squads for MOTM selector
        $playerModel = new \App\Models\Player();
        $homeSquad = $playerModel->getByTeam($resolved['homeTeam']['id']);
        $awaySquad = $playerModel->getByTeam($resolved['awayTeam']['id']);

        $this->render('fixtures/detail', [
            'title' => 'Edit Fixture: ' . $fixtureDetail['homeTeamName'] . ' vs ' . $fixtureDetail['awayTeamName'],
            'fixtureType' => $type,
            'competition' => $resolved['competition'],
            'fixture' => $fixtureDetail,
            'fixtureSlug' => $fixtureSlug,
            'photos' => $photos,
            'referees' => $referees,
            'homeSquad' => $homeSquad,
            'awaySquad' => $awaySquad,
        ]);
    }

    /**
     * Update fixture rich content details.
     */
    public function updateFixtureDetail(string $type, string $competitionSlug, string $fixtureSlug): void
    {
        $resolved = $this->resolveFixture($type, $competitionSlug, $fixtureSlug);
        if (!$resolved) {
            return;
        }

        $details = [
            'status' => $this->post('status'),
            'refereeId' => $this->post('referee_id'),
            'matchReport' => $this->post('match_report'),
            'liveStreamUrl' => $this->post('live_stream_url'),
            'fullMatchUrl' => $this->post('full_match_url'),
            'highlightsUrl' => $this->post('highlights_url'),
            'motmPlayerId' => $this->post('motm_player_id'),
        ];

        $resolved['model']->updateFixtureRichContent($resolved['fixture']['id'], $details);

        $this->flash('success', 'Fixture details updated successfully');
        $this->redirect("/admin/fixture/{$type}/{$competitionSlug}/{$fixtureSlug}");
    }

    /**
     * Upload photos for a fixture.
     */
    public function uploadPhotos(string $type, string $competitionSlug, string $fixtureSlug): void
    {
        $redirectUrl = "/admin/fixture/{$type}/{$competitionSlug}/{$fixtureSlug}";

        $resolved = $this->resolveFixture($type, $competitionSlug, $fixtureSlug);
        if (!$resolved) {
            return;
        }

        $fixture = $resolved['fixture'];

        // Handle file uploads
        if (!isset($_FILES['photos']) || !is_array($_FILES['photos']['name'])) {
            $this->flash('error', 'No files uploaded');
            $this->redirect($redirectUrl);
            return;
        }

        $photoModel = new \App\Models\FixturePhoto();
        $uploadCount = 0;
        $errorCount = 0;
        $errors = [];

        $sortOrder = $photoModel->getMaxSortOrder($type, $fixture['id']);
        $uploadPath = 'fixtures/' . $competitionSlug . '/' . $fixtureSlug;

        $fileCount = count($_FILES['photos']['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file = [
                'name' => $_FILES['photos']['name'][$i],
                'type' => $_FILES['photos']['type'][$i],
                'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                'error' => $_FILES['photos']['error'][$i],
                'size' => $_FILES['photos']['size'][$i],
            ];

            $caption = $_POST['captions'][$i] ?? '';
            $result = \Core\Upload::uploadFile($file, $uploadPath);

            if ($result['success']) {
                $sortOrder++;
                $photoModel->create([
                    'fixture_id' => $fixture['id'],
                    'fixture_type' => $type,
                    'file_path' => $uploadPath . '/' . $result['filename'],
                    'caption' => $caption,
                    'sort_order' => $sortOrder,
                ]);
                $uploadCount++;
            } else {
                $errorCount++;
                $errors[] = $result['error'];
            }
        }

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

        $this->redirect($redirectUrl);
    }

    /**
     * Delete a fixture photo.
     */
    public function deletePhoto(string $type, string $competitionSlug, string $fixtureSlug, int $photoId): void
    {
        $photoModel = new \App\Models\FixturePhoto();
        $filePath = $photoModel->deletePhoto($photoId);

        if ($filePath) {
            $fullPath = BASE_PATH . '/uploads/' . $filePath;
            if (file_exists($fullPath)) {
                unlink($fullPath);

                // Clean up empty directory
                $directory = dirname($fullPath);
                if (is_dir($directory)) {
                    $files = array_diff(scandir($directory), ['.', '..']);
                    if (empty($files)) {
                        rmdir($directory);
                    }
                }
            }
            $this->flash('success', 'Photo deleted successfully');
        } else {
            $this->flash('error', 'Photo not found');
        }

        $this->redirect("/admin/fixture/{$type}/{$competitionSlug}/{$fixtureSlug}");
    }

    /**
     * Resolve a fixture from URL parameters.
     * Validates type, parses slug, loads teams/competition, and finds the fixture.
     *
     * @return array{fixture: array, competition: array, model: \App\Models\League|\App\Models\Cup, homeTeam: array, awayTeam: array}|null
     */
    private function resolveFixture(string $type, string $competitionSlug, string $fixtureSlug): ?array
    {
        if (!in_array($type, ['league', 'cup'])) {
            $this->flash('error', 'Invalid fixture type');
            $this->redirect('/admin');
            return null;
        }

        if (!preg_match('/^(.+)-vs-(.+)$/', $fixtureSlug, $matches)) {
            $this->flash('error', 'Invalid fixture');
            $this->redirect('/admin');
            return null;
        }

        $teamModel = new \App\Models\Team();
        $homeTeam = $teamModel->findWhere('slug', $matches[1]);
        $awayTeam = $teamModel->findWhere('slug', $matches[2]);

        if (!$homeTeam || !$awayTeam) {
            $this->flash('error', 'Teams not found');
            $this->redirect('/admin');
            return null;
        }

        if ($type === 'league') {
            $model = new \App\Models\League();
            $competition = $model->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->flash('error', 'Competition not found');
                $this->redirect('/admin/leagues');
                return null;
            }

            $fixtures = $model->getFixtures($competition['id']);
            $fixture = $this->findFixtureByTeamIds($fixtures, $homeTeam['id'], $awayTeam['id']);
        } else {
            $model = new \App\Models\Cup();
            $competition = $model->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->flash('error', 'Competition not found');
                $this->redirect('/admin/cups');
                return null;
            }

            $rounds = $model->getRounds($competition['id']);
            $fixture = $this->findFixtureInRounds($rounds, $homeTeam['id'], $awayTeam['id']);
        }

        if (!$fixture) {
            $this->flash('error', 'Fixture not found');
            $this->redirect('/admin');
            return null;
        }

        return [
            'fixture' => $fixture,
            'competition' => $competition,
            'model' => $model,
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
        ];
    }

    /**
     * Find fixture by team IDs in a fixture list.
     * Prefers matches with results or video content if multiple exist.
     */
    private function findFixtureByTeamIds(array $fixtures, int|string $homeId, int|string $awayId): ?array
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
    private function findFixtureInRounds(array $rounds, int|string $homeId, int|string $awayId): ?array
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
}
