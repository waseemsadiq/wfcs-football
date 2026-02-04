<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\League;
use App\Models\Team;
use App\Models\Season;

/**
 * Controller for managing football leagues.
 */
class LeaguesController extends CompetitionController
{
    public function __construct()
    {
        parent::__construct();
        $this->competition = new League();
        $this->team = new Team();
        $this->season = new Season();
    }

    protected function getEntityType(): string
    {
        return 'league';
    }

    protected function getEntityTypePlural(): string
    {
        return 'leagues';
    }

    protected function getViewPrefix(): string
    {
        return 'leagues';
    }

    /**
     * Display a single league with standings.
     */
    public function show(string $slug): void
    {
        $league = $this->competition->findWhere('slug', $slug);

        if (!$league) {
            $this->flash('error', 'League not found.');
            $this->redirect('/admin/leagues');
            return;
        }

        $teams = $this->team->all();
        $fixtures = $this->enrichFixturesWithTeamData($league['fixtures'] ?? [], $teams);

        // Calculate standings
        $standings = $this->calculateStandings($fixtures, $league['teamIds'] ?? []);

        // Enrich standings with team details
        foreach ($standings as &$standing) {
            foreach ($teams as $team) {
                if ($team['id'] == $standing['teamId']) {
                    $standing['teamName'] = $team['name'];
                    $standing['teamColour'] = $team['colour'] ?? '#1a5f2a';
                    break;
                }
            }
        }

        $this->render('leagues/show', [
            'title' => $league['name'],
            'currentPage' => 'leagues',
            'league' => $league,
            'fixtures' => $fixtures,
            'standings' => $standings,
        ]);
    }

    /**
     * Show fixtures page for editing results.
     */
    public function fixtures(string $slug): void
    {
        $league = $this->competition->findWhere('slug', $slug);

        if (!$league) {
            $this->flash('error', 'League not found.');
            $this->redirect('/admin/leagues');
            return;
        }

        $teams = $this->team->all();
        $fixtures = $this->enrichFixturesWithTeamData($league['fixtures'] ?? [], $teams);

        usort($fixtures, fn($a, $b) => strcmp($a['date'], $b['date']));

        $this->render('leagues/fixtures', [
            'title' => $league['name'] . ' Fixtures',
            'currentPage' => 'leagues',
            'league' => $league,
            'fixtures' => $fixtures,
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
            $this->redirect('/admin/leagues/' . $slug . '/fixtures');
            return;
        }

        /** @var League $leagueModel */
        $leagueModel = $this->competition;
        $league = $leagueModel->findWhere('slug', $slug);

        if (!$league) {
            $this->flash('error', 'League not found.');
            $this->redirect('/admin/leagues');
            return;
        }

        $fixtureId = $this->post('fixtureId');
        $homeScore = $this->post('homeScore');
        $awayScore = $this->post('awayScore');
        $date = $this->post('date');
        $time = $this->post('time');

        if ($date && !$this->validateDate($date)) {
            $this->flash('error', 'Invalid date format. Please use YYYY-MM-DD.');
            $this->redirect('/admin/leagues/' . $slug . '/fixtures');
            return;
        }

        if ($time && !$this->validateTime($time)) {
            $this->flash('error', 'Invalid time format. Please use HH:MM.');
            $this->redirect('/admin/leagues/' . $slug . '/fixtures');
            return;
        }

        // Normalize time for database storage
        if ($time) {
            $time = $this->normalizeTime($time);
        }

        // Update date/time if provided
        if ($date && $time) {
            $leagueModel->updateFixtureDateTime($league['id'], $fixtureId, $date, $time);
        }

        // Update result if scores provided
        if ($homeScore !== null && $homeScore !== '' && $awayScore !== null && $awayScore !== '') {
            // Validate scores are non-negative integers
            if (!is_numeric($homeScore) || !is_numeric($awayScore) || (int) $homeScore < 0 || (int) $awayScore < 0) {
                if ($this->isAjaxRequest()) {
                    $this->json(['success' => false, 'error' => 'Scores must be non-negative numbers.']);
                    return;
                }
                $this->flash('error', 'Scores must be non-negative numbers.');
                $this->redirect('/admin/leagues/' . $slug . '/fixtures');
                return;
            }

            $result = [
                'homeScore' => (int) $homeScore,
                'awayScore' => (int) $awayScore,
                'homeScorers' => $this->sanitizeString($this->post('homeScorers', ''), 500),
                'awayScorers' => $this->sanitizeString($this->post('awayScorers', ''), 500),
                'homeCards' => $this->sanitizeString($this->post('homeCards', ''), 500),
                'awayCards' => $this->sanitizeString($this->post('awayCards', ''), 500),
            ];
            $leagueModel->updateFixtureResult($league['id'], $fixtureId, $result);
        }

        if ($this->isAjaxRequest()) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            $this->json(['success' => true, 'message' => 'Fixture updated.']);
            return;
        }

        $this->flash('success', 'Fixture updated.');
        $this->redirect('/admin/leagues/' . $slug . '/fixtures');
    }

    /**
     * Regenerate fixtures for a league.
     * Deletes only unplayed fixtures and generates new ones, preserving played fixtures.
     */
    public function regenerateFixtures(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission.');
            $this->redirect('/admin/leagues/' . $slug . '/fixtures');
            return;
        }

        /** @var League $leagueModel */
        $leagueModel = $this->competition;
        $league = $leagueModel->findWhere('slug', $slug);

        if (!$league) {
            $this->flash('error', 'League not found.');
            $this->redirect('/admin/leagues');
            return;
        }

        $teamIds = $league['teamIds'] ?? [];
        if (count($teamIds) < 2) {
            $this->flash('error', 'Not enough teams to generate fixtures.');
            $this->redirect('/admin/leagues/' . $slug . '/fixtures');
            return;
        }

        $startDate = $this->post('startDate', $league['startDate'] ?? date('Y-m-d')) ?: date('Y-m-d');
        $frequency = $this->post('frequency', $league['frequency'] ?? 'weekly');
        $matchTime = $this->post('matchTime', $league['matchTime'] ?? '15:00');

        // Normalize values for database
        $frequency = $this->normalizeFrequency($frequency);
        $matchTime = $this->normalizeTime($matchTime);

        // Delete only unplayed fixtures (preserves played fixtures with scores)
        $leagueModel->deleteUnplayedFixtures($league['id']);

        // Generate new fixtures without deleting existing ones
        $success = $leagueModel->generateFixtures(
            $league['id'],
            $teamIds,
            $startDate,
            $frequency,
            $matchTime,
            deleteExisting: false
        );

        if (!$success) {
            $this->flash('error', 'Failed to generate fixtures.');
            $this->redirect('/admin/leagues/' . $slug . '/fixtures');
            return;
        }

        // Update league metadata
        $leagueModel->update($league['id'], [
            'start_date' => $startDate,
            'frequency' => $frequency,
            'match_time' => $matchTime
        ]);

        // Get updated fixture count
        $fixtureCount = $leagueModel->getFixturesCount($league['id']);

        if ($this->isAjaxRequest()) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            $this->json([
                'success' => true,
                'message' => 'Fixtures regenerated successfully.',
                'count' => $fixtureCount
            ]);
            return;
        }

        $this->flash('success', 'Fixtures regenerated successfully.');
        $this->redirect('/admin/leagues/' . $slug . '/fixtures');
    }

    /**
     * Calculate league standings from fixtures.
     */
    protected function calculateStandings(array $fixtures, array $teamIds): array
    {
        $standings = [];

        foreach ($teamIds as $teamId) {
            $standings[$teamId] = [
                'teamId' => $teamId,
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'goalsFor' => 0,
                'goalsAgainst' => 0,
                'goalDifference' => 0,
                'points' => 0,
            ];
        }

        foreach ($fixtures as $fixture) {
            if (!isset($fixture['result'])) {
                continue;
            }

            $result = $fixture['result'];
            $homeId = $fixture['homeTeamId'];
            $awayId = $fixture['awayTeamId'];
            $homeScore = $result['homeScore'] ?? 0;
            $awayScore = $result['awayScore'] ?? 0;

            if (!isset($standings[$homeId]) || !isset($standings[$awayId])) {
                continue;
            }

            $standings[$homeId]['played']++;
            $standings[$awayId]['played']++;
            $standings[$homeId]['goalsFor'] += $homeScore;
            $standings[$homeId]['goalsAgainst'] += $awayScore;
            $standings[$awayId]['goalsFor'] += $awayScore;
            $standings[$awayId]['goalsAgainst'] += $homeScore;

            if ($homeScore > $awayScore) {
                $standings[$homeId]['won']++;
                $standings[$homeId]['points'] += 3;
                $standings[$awayId]['lost']++;
            } elseif ($homeScore < $awayScore) {
                $standings[$awayId]['won']++;
                $standings[$awayId]['points'] += 3;
                $standings[$homeId]['lost']++;
            } else {
                $standings[$homeId]['drawn']++;
                $standings[$awayId]['drawn']++;
                $standings[$homeId]['points']++;
                $standings[$awayId]['points']++;
            }
        }

        foreach ($standings as &$standing) {
            $standing['goalDifference'] = $standing['goalsFor'] - $standing['goalsAgainst'];
        }

        usort($standings, function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] - $a['points'];
            }
            if ($a['goalDifference'] !== $b['goalDifference']) {
                return $b['goalDifference'] - $a['goalDifference'];
            }
            return $b['goalsFor'] - $a['goalsFor'];
        });

        return array_values($standings);
    }

    /**
     * Enrich fixtures with team names and colours.
     */
    protected function enrichFixturesWithTeamData(array $fixtures, array $teams): array
    {
        $teamMap = [];
        foreach ($teams as $team) {
            $teamMap[$team['id']] = $team;
        }

        foreach ($fixtures as &$fixture) {
            $homeId = $fixture['homeTeamId'] ?? null;
            $awayId = $fixture['awayTeamId'] ?? null;

            if ($homeId && isset($teamMap[$homeId])) {
                $fixture['homeTeamName'] = $teamMap[$homeId]['name'];
                $fixture['homeTeamColour'] = $teamMap[$homeId]['colour'] ?? '#1a5f2a';
            }

            if ($awayId && isset($teamMap[$awayId])) {
                $fixture['awayTeamName'] = $teamMap[$awayId]['name'];
                $fixture['awayTeamColour'] = $teamMap[$awayId]['colour'] ?? '#1a5f2a';
            }
        }

        return $fixtures;
    }
}
