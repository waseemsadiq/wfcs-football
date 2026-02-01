<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\League;
use App\Models\Team;
use App\Models\Season;

/**
 * Controller for managing football leagues.
 */
class LeaguesController extends Controller
{
    private League $league;
    private Team $team;
    private Season $season;

    public function __construct()
    {
        parent::__construct();
        $this->league = new League();
        $this->team = new Team();
        $this->season = new Season();
    }

    /**
     * Display all leagues.
     */
    public function index(): void
    {
        $leagues = $this->league->all();

        // Add season name to each league
        foreach ($leagues as &$league) {
            $season = $this->season->find($league['seasonId'] ?? '');
            $league['seasonName'] = $season['name'] ?? 'Unknown';
        }

        $this->render('leagues/index', [
            'title' => 'Leagues',
            'currentPage' => 'leagues',
            'leagues' => $leagues,
        ]);
    }

    /**
     * Show form to create a new league.
     */
    public function create(): void
    {
        $teams = $this->team->allSorted();
        $seasons = $this->season->allSorted();

        $this->render('leagues/create', [
            'title' => 'Create League',
            'currentPage' => 'leagues',
            'teams' => $teams,
            'seasons' => $seasons,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Store a new league.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/leagues/create');
            return;
        }

        $missing = $this->validateRequired(['name', 'seasonId', 'startDate']);
        if (!empty($missing)) {
            $this->flash('error', 'Please fill in all required fields.');
            $this->redirect('/admin/leagues/create');
            return;
        }

        // Validate and sanitize inputs
        $name = $this->sanitizeString($this->post('name'), 100);
        $seasonId = $this->post('seasonId');
        $startDate = $this->post('startDate');
        $frequency = $this->post('frequency', 'weekly');
        $matchTime = $this->post('matchTime', '15:00');

        if (!$this->validateLength($name, 1, 100)) {
            $this->flash('error', 'League name must be between 1 and 100 characters.');
            $this->redirect('/admin/leagues/create');
            return;
        }

        if (!$this->validateDate($startDate)) {
            $this->flash('error', 'Invalid date format. Please use YYYY-MM-DD.');
            $this->redirect('/admin/leagues/create');
            return;
        }

        if (!$this->validateTime($matchTime)) {
            $this->flash('error', 'Invalid time format. Please use HH:MM.');
            $this->redirect('/admin/leagues/create');
            return;
        }

        $frequency = $this->normalizeFrequency($frequency);

        $teamIds = $this->post('teamIds', []);
        if (!is_array($teamIds) || count($teamIds) < 2) {
            $this->flash('error', 'Please select at least 2 teams for the league.');
            $this->redirect('/admin/leagues/create');
            return;
        }

        // Generate fixtures
        $fixtures = $this->league->generateFixtures($teamIds, $startDate, $frequency, $matchTime);

        $league = $this->league->create([
            'name' => $name,
            'seasonId' => $seasonId,
            'teamIds' => $teamIds,
            'fixtures' => $fixtures,
            'startDate' => $startDate,
            'frequency' => $frequency,
            'matchTime' => $matchTime,
        ]);

        // Add league to season
        $this->season->addLeague($seasonId, $league['id']);

        $this->flash('success', 'League created with ' . count($fixtures) . ' fixtures generated.');
        $this->redirect('/admin/leagues/' . $league['slug']);
    }

    /**
     * Display a single league with standings and fixtures.
     */
    public function show(string $slug): void
    {
        $league = $this->league->findWhere('slug', $slug);

        if (!$league) {
            $this->flash('error', 'League not found.');
            $this->redirect('/admin/leagues');
            return;
        }

        $teams = $this->team->all();
        $standings = $this->league->calculateStandings($league, $teams);

        // Enrich fixtures with team names
        $fixtures = $this->enrichLeagueFixtures($league['fixtures'] ?? [], $teams);

        // Sort fixtures by date
        usort($fixtures, fn($a, $b) => strcmp($a['date'], $b['date']));

        $this->render('leagues/show', [
            'title' => $league['name'],
            'currentPage' => 'leagues',
            'league' => $league,
            'standings' => $standings,
            'fixtures' => $fixtures,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Show form to edit league details.
     */
    public function edit(string $slug): void
    {
        $league = $this->league->findWhere('slug', $slug);

        if (!$league) {
            $this->flash('error', 'League not found.');
            $this->redirect('/admin/leagues');
            return;
        }

        $this->render('leagues/edit', [
            'title' => 'Edit ' . $league['name'],
            'currentPage' => 'leagues',
            'league' => $league,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Update league details.
     */
    public function update(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/leagues/' . $slug . '/edit');
            return;
        }

        $league = $this->league->findWhere('slug', $slug);
        if (!$league) {
            $this->flash('error', 'League not found.');
            $this->redirect('/admin/leagues');
            return;
        }

        // Validate and sanitize inputs
        $newName = $this->sanitizeString($this->post('name', $league['name']), 100);
        $startDate = $this->post('startDate', $league['startDate'] ?? '');
        $frequency = $this->post('frequency', $league['frequency'] ?? 'weekly');
        $matchTime = $this->post('matchTime', $league['matchTime'] ?? '15:00');

        if (!$this->validateLength($newName, 1, 100)) {
            $this->flash('error', 'League name must be between 1 and 100 characters.');
            $this->redirect('/admin/leagues/' . $slug . '/edit');
            return;
        }

        if ($startDate && !$this->validateDate($startDate)) {
            $this->flash('error', 'Invalid date format. Please use YYYY-MM-DD.');
            $this->redirect('/admin/leagues/' . $slug . '/edit');
            return;
        }

        if ($matchTime && !$this->validateTime($matchTime)) {
            $this->flash('error', 'Invalid time format. Please use HH:MM.');
            $this->redirect('/admin/leagues/' . $slug . '/edit');
            return;
        }

        $frequency = $this->normalizeFrequency($frequency);

        $this->league->update($league['id'], [
            'name' => $newName,
            'startDate' => $startDate,
            'frequency' => $frequency,
            'matchTime' => $matchTime,
        ]);

        $newSlug = League::slugify($newName);

        $this->flash('success', 'League updated.');
        $this->redirect('/admin/leagues/' . $newSlug);
    }

    /**
     * Delete a league.
     */
    public function delete(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/leagues');
            return;
        }

        $league = $this->league->findWhere('slug', $slug);

        if ($league) {
            $seasonId = $league['seasonId'];
            $this->league->delete($league['id']);
            $this->season->removeLeague($seasonId, $league['id']);

            $this->flash('success', 'League deleted.');
        } else {
            $this->flash('error', 'League not found.');
        }

        $this->redirect('/admin/leagues');
    }

    /**
     * Show fixtures page for editing results.
     */
    public function fixtures(string $slug): void
    {
        $league = $this->league->findWhere('slug', $slug);

        if (!$league) {
            $this->flash('error', 'League not found.');
            $this->redirect('/admin/leagues');
            return;
        }

        $teams = $this->team->all();
        $fixtures = $this->enrichLeagueFixtures($league['fixtures'] ?? [], $teams);

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

        $league = $this->league->findWhere('slug', $slug);
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

        // Update date/time if provided
        if ($date && $time) {
            $this->league->updateFixtureDateTime($league['id'], $fixtureId, $date, $time);
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
            $this->league->updateFixtureResult($league['id'], $fixtureId, $result);
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
     */
    public function regenerateFixtures(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission.');
            $this->redirect('/admin/leagues/' . $slug . '/fixtures');
            return;
        }

        $league = $this->league->findWhere('slug', $slug);
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

        $finalFixtures = $this->mergePlayedWithNewFixtures(
            $league['fixtures'] ?? [],
            $teamIds,
            $startDate,
            $frequency,
            $matchTime
        );

        $this->league->update($league['id'], [
            'fixtures' => $finalFixtures,
            'startDate' => $startDate,
            'frequency' => $frequency,
            'matchTime' => $matchTime
        ]);

        if ($this->isAjaxRequest()) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            $this->json([
                'success' => true,
                'message' => 'Fixtures regenerated successfully.',
                'count' => count($finalFixtures)
            ]);
        }

        $this->flash('success', 'Fixtures regenerated successfully.');
        $this->redirect('/admin/leagues/' . $slug . '/fixtures');
    }

    /**
     * Merge played fixtures with newly generated ones, preserving results.
     *
     * @param array $existingFixtures Current fixtures (may have results)
     * @param array $teamIds Team IDs for fixture generation
     * @param string $startDate Start date for new fixtures
     * @param string $frequency Fixture frequency
     * @param string $matchTime Default match time
     * @return array Merged and sorted fixtures
     */
    private function mergePlayedWithNewFixtures(
        array $existingFixtures,
        array $teamIds,
        string $startDate,
        string $frequency,
        string $matchTime
    ): array {
        $playedFixtures = $this->extractPlayedFixtures($existingFixtures);
        $playedPairings = $this->extractFixturePairings($playedFixtures);

        $newFixtures = $this->league->generateFixtures($teamIds, $startDate, $frequency, $matchTime);
        $unplayedNewFixtures = $this->filterUnplayedFixtures($newFixtures, $playedPairings);

        $busyDates = $this->buildBusyDatesMap($playedFixtures);
        $rescheduledFixtures = $this->resolveSchedulingConflicts($unplayedNewFixtures, $busyDates);

        $finalFixtures = array_merge($playedFixtures, $rescheduledFixtures);
        usort($finalFixtures, fn($a, $b) => strcmp($a['date'] . $a['time'], $b['date'] . $b['time']));

        return $finalFixtures;
    }

    /**
     * Extract fixtures that have been played (have results).
     */
    private function extractPlayedFixtures(array $fixtures): array
    {
        return array_filter($fixtures, fn($f) => ($f['result'] ?? null) !== null);
    }

    /**
     * Extract home-away pairings from fixtures for comparison.
     */
    private function extractFixturePairings(array $fixtures): array
    {
        return array_map(fn($f) => $f['homeTeamId'] . '-' . $f['awayTeamId'], $fixtures);
    }

    /**
     * Filter out fixtures that match already played pairings.
     */
    private function filterUnplayedFixtures(array $fixtures, array $playedPairings): array
    {
        return array_filter($fixtures, function ($f) use ($playedPairings) {
            $pairing = $f['homeTeamId'] . '-' . $f['awayTeamId'];
            return !in_array($pairing, $playedPairings, true);
        });
    }

    /**
     * Build a map of dates when each team is busy (has a fixture).
     */
    private function buildBusyDatesMap(array $fixtures): array
    {
        $busyDates = [];
        foreach ($fixtures as $f) {
            $busyDates[$f['homeTeamId']][$f['date']] = true;
            $busyDates[$f['awayTeamId']][$f['date']] = true;
        }
        return $busyDates;
    }

    /**
     * Resolve scheduling conflicts by moving fixtures to available dates.
     *
     * @param array $fixtures Fixtures to check for conflicts
     * @param array $busyDates Map of team busy dates (modified by reference)
     * @return array Fixtures with resolved dates
     */
    private function resolveSchedulingConflicts(array $fixtures, array &$busyDates): array
    {
        $resolved = [];

        foreach ($fixtures as $fixture) {
            $home = $fixture['homeTeamId'];
            $away = $fixture['awayTeamId'];
            $date = $fixture['date'];

            while (isset($busyDates[$home][$date]) || isset($busyDates[$away][$date])) {
                $date = date('Y-m-d', strtotime($date . ' +1 day'));
            }

            $fixture['date'] = $date;
            $busyDates[$home][$date] = true;
            $busyDates[$away][$date] = true;

            $resolved[] = $fixture;
        }

        return $resolved;
    }
}
