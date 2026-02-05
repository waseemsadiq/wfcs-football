<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cup;
use App\Models\Team;
use App\Models\Season;

/**
 * Controller for managing cup competitions.
 */
class CupsController extends CompetitionController
{
    public function __construct()
    {
        parent::__construct();
        $this->competition = new Cup();
        $this->team = new Team();
        $this->season = new Season();
    }

    protected function getEntityType(): string
    {
        return 'cup';
    }

    protected function getEntityTypePlural(): string
    {
        return 'cups';
    }

    protected function getViewPrefix(): string
    {
        return 'cups';
    }

    /**
     * Display a single cup with bracket.
     */
    public function show(string $slug): void
    {
        $cup = $this->competition->findWhere('slug', $slug);

        if (!$cup) {
            $this->flash('error', 'Cup not found.');
            $this->redirect('/admin/cups');
            return;
        }

        $teams = $this->team->all();
        $rounds = $this->enrichCupRoundsWithTeamData($cup['rounds'] ?? [], $teams);

        $this->render('cups/show', [
            'title' => $cup['name'],
            'currentPage' => 'cups',
            'cup' => $cup,
            'rounds' => $rounds,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Show fixtures page for editing results.
     */
    public function fixtures(string $slug): void
    {
        /** @var array $cup */
        $cup = $this->competition->findWhere('slug', $slug);

        if (!$cup) {
            $this->flash('error', 'Cup not found.');
            $this->redirect('/admin/cups');
            return;
        }

        $teams = $this->team->all();
        $rounds = $this->enrichCupRoundsWithTeamData($cup['rounds'] ?? [], $teams);

        $this->render('cups/fixtures', [
            'title' => $cup['name'] . ' Fixtures',
            'currentPage' => 'cups',
            'cup' => $cup,
            'rounds' => $rounds,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Update fixtures (results, dates, times).
     */
    public function updateFixtures(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/cups/' . $slug . '/fixtures');
            return;
        }

        /** @var Cup $cupModel */
        $cupModel = $this->competition;
        /** @var array $cup */
        $cup = $cupModel->findWhere('slug', $slug);

        if (!$cup) {
            $this->flash('error', 'Cup not found.');
            $this->redirect('/admin/cups');
            return;
        }

        $fixtureId = $this->post('fixtureId');
        $homeScore = $this->post('homeScore');
        $awayScore = $this->post('awayScore');
        $date = $this->post('date');
        $time = $this->post('time');

        if ($date && !$this->validateDate($date)) {
            $this->flash('error', 'Invalid date format. Please use YYYY-MM-DD.');
            $this->redirect('/admin/cups/' . $slug . '/fixtures');
            return;
        }

        if ($time && !$this->validateTime($time)) {
            $this->flash('error', 'Invalid time format. Please use HH:MM.');
            $this->redirect('/admin/cups/' . $slug . '/fixtures');
            return;
        }

        // Normalize time for database storage
        if ($time) {
            $time = $this->normalizeTime($time);
        }

        // Update date/time if provided
        if ($date && $time) {
            $cupModel->updateFixtureDateTime($cup['id'], $fixtureId, $date, $time);
        }

        // Update result if scores provided or cleared
        if ($fixtureId && (($homeScore !== '' && $awayScore !== '') || ($homeScore === '' && $awayScore === ''))) {
            if ($homeScore !== '') {
                // Validate scores are non-negative integers
                if (!is_numeric($homeScore) || !is_numeric($awayScore) || (int) $homeScore < 0 || (int) $awayScore < 0) {
                    if ($this->isAjaxRequest()) {
                        $this->json(['success' => false, 'error' => 'Scores must be non-negative numbers.']);
                        return;
                    }
                    $this->flash('error', 'Scores must be non-negative numbers.');
                    $this->redirect('/admin/cups/' . $slug . '/fixtures');
                    return;
                }

                $result = [
                    'homeScore' => (int) $homeScore,
                    'awayScore' => (int) $awayScore,
                    'homeScorers' => $this->sanitizeString($this->post('homeScorers', ''), 500),
                    'awayScorers' => $this->sanitizeString($this->post('awayScorers', ''), 500),
                    'homeCards' => $this->sanitizeString($this->post('homeCards', ''), 500),
                    'awayCards' => $this->sanitizeString($this->post('awayCards', ''), 500),
                    'extraTime' => $this->post('extraTime') === '1',
                    'homeScoreET' => $this->post('homeScoreET') !== '' ? (int) $this->post('homeScoreET') : null,
                    'awayScoreET' => $this->post('awayScoreET') !== '' ? (int) $this->post('awayScoreET') : null,
                    'penalties' => $this->post('penalties') === '1',
                    'homePens' => $this->post('homePens') !== '' ? (int) $this->post('homePens') : null,
                    'awayPens' => $this->post('awayPens') !== '' ? (int) $this->post('awayPens') : null,
                ];
            } else {
                // Clear result
                $result = [
                    'homeScore' => null,
                    'awayScore' => null,
                    'homeScorers' => '',
                    'awayScorers' => '',
                    'homeCards' => '',
                    'awayCards' => '',
                    'extraTime' => false,
                    'homeScoreET' => null,
                    'awayScoreET' => null,
                    'penalties' => false,
                    'homePens' => null,
                    'awayPens' => null,
                ];
            }
            $cupModel->updateFixtureResult($cup['id'], $fixtureId, $result);
        } elseif ($fixtureId && ($homeScore === '' || $awayScore === '')) {
            $this->flash('error', 'Both scores must be provided to save a result.');
            $this->redirect('/admin/cups/' . $slug . '/fixtures');
            return;
        }

        if ($this->isAjaxRequest()) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            $this->json(['success' => true, 'message' => 'Fixture updated.']);
            return;
        }

        $this->flash('success', 'Fixture updated.');
        $this->redirect('/admin/cups/' . $slug . '/fixtures');
    }

    /**
     * Regenerate remaining bracket fixtures for a cup.
     */
    /**
     * Regenerate remaining bracket fixtures for a cup.
     */
    public function regenerateFixtures(string $slug): void
    {
        // Enforce JSON response for AJAX requests even on detailed errors
        try {
            if (!$this->validateCsrf()) {
                throw new \Exception('Invalid form submission.');
            }

            /** @var Cup $cupModel */
            $cupModel = $this->competition;
            $cup = $cupModel->findWhere('slug', $slug);

            if (!$cup) {
                throw new \Exception('Cup not found.');
            }

            $teamIds = $cup['teamIds'] ?? [];
            if (count($teamIds) < 2) {
                throw new \Exception('Not enough teams to generate bracket.');
            }

            $startDate = $this->post('startDate', $cup['startDate'] ?? date('Y-m-d')) ?: date('Y-m-d');
            $frequency = $this->post('frequency', $cup['frequency'] ?? 'weekly');
            $matchTime = $this->post('matchTime', $cup['matchTime'] ?? '15:00');

            // Normalize values for database
            $frequency = $this->normalizeFrequency($frequency);
            $matchTime = $this->normalizeTime($matchTime);

            // Check if any matches have been played (have a result)
            $hasPlayedMatches = false;
            foreach ($cup['rounds'] ?? [] as $round) {
                foreach ($round['fixtures'] ?? [] as $fixture) {
                    if ($fixture['result'] !== null) {
                        $hasPlayedMatches = true;
                        break 2;
                    }
                }
            }

            if ($hasPlayedMatches) {
                // Reschedule only unplayed fixtures
                $success = $cupModel->rescheduleUnplayed(
                    $cup['id'],
                    $startDate,
                    $frequency,
                    $matchTime
                );
                $message = 'Unplayed fixtures rescheduled successfully.';
            } else {
                // Full regeneration
                $success = $cupModel->generateBracket(
                    $cup['id'],
                    $teamIds,
                    $startDate,
                    $frequency,
                    $matchTime
                );
                $message = 'Bracket regenerated successfully.';
            }

            if (!$success) {
                throw new \Exception('Failed to update fixtures.');
            }

            // Update cup metadata
            $cupModel->update($cup['id'], [
                'start_date' => $startDate,
                'frequency' => $frequency,
                'match_time' => $matchTime
            ]);

            // Get updated rounds
            $updatedCup = $cupModel->find($cup['id']);
            $roundCount = count($updatedCup['rounds'] ?? []);

            if ($this->isAjaxRequest()) {
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_write_close();
                }
                $this->json([
                    'success' => true,
                    'message' => $message,
                    'rounds' => $roundCount
                ]);
                return;
            }

            $this->flash('success', $message);
            $this->redirect('/admin/cups/' . $slug . '/fixtures');

        } catch (\Throwable $e) {
            if ($this->isAjaxRequest()) {
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_write_close();
                }
                $this->json(['success' => false, 'error' => $e->getMessage()]);
                return;
            }

            $this->flash('error', $e->getMessage());
            $this->redirect('/admin/cups/' . $slug . '/fixtures');
        }
    }

    /**
     * Enrich rounds with team names and colours.
     */
    protected function enrichCupRoundsWithTeamData(array $rounds, array $teams): array
    {
        $teamMap = [];
        foreach ($teams as $team) {
            $teamMap[$team['id']] = $team;
        }

        foreach ($rounds as &$round) {
            if (!isset($round['fixtures']) || !is_array($round['fixtures'])) {
                continue;
            }
            foreach ($round['fixtures'] as &$fixture) {
                $homeId = $fixture['homeTeamId'] ?? null;
                $awayId = $fixture['awayTeamId'] ?? null;

                if ($homeId && isset($teamMap[$homeId])) {
                    $fixture['homeTeamName'] = $teamMap[$homeId]['name'];
                    $fixture['homeTeamColour'] = $teamMap[$homeId]['colour'] ?? '#1a5f2a';
                } else {
                    $fixture['homeTeamName'] = 'TBD';
                    $fixture['homeTeamColour'] = '#666666';
                }

                if ($awayId && isset($teamMap[$awayId])) {
                    $fixture['awayTeamName'] = $teamMap[$awayId]['name'];
                    $fixture['awayTeamColour'] = $teamMap[$awayId]['colour'] ?? '#1a5f2a';
                } else {
                    $fixture['awayTeamName'] = 'TBD';
                    $fixture['awayTeamColour'] = '#666666';
                }
            }
        }

        return $rounds;
    }
}
