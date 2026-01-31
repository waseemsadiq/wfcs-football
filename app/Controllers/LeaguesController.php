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
        $fixtures = [];
        foreach ($league['fixtures'] ?? [] as $fixture) {
            $homeTeam = $this->findById($teams, $fixture['homeTeamId']);
            $awayTeam = $this->findById($teams, $fixture['awayTeamId']);
            $fixture['homeTeamName'] = $homeTeam['name'] ?? 'Unknown';
            $fixture['awayTeamName'] = $awayTeam['name'] ?? 'Unknown';
            $fixture['homeTeamColour'] = $homeTeam['colour'] ?? '#000';
            $fixture['awayTeamColour'] = $awayTeam['colour'] ?? '#000';
            $fixtures[] = $fixture;
        }

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
        if (!$league) {
            $this->flash('error', 'League not found.');
            $this->redirect('/admin/leagues');
            return;
        }

        $this->league->delete($league['id']);
        $this->flash('success', 'League deleted.');
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
        $fixtures = [];

        foreach ($league['fixtures'] ?? [] as $fixture) {
            $homeTeam = $this->findById($teams, $fixture['homeTeamId']);
            $awayTeam = $this->findById($teams, $fixture['awayTeamId']);
            $fixture['homeTeamName'] = $homeTeam['name'] ?? 'Unknown';
            $fixture['awayTeamName'] = $awayTeam['name'] ?? 'Unknown';
            $fixtures[] = $fixture;
        }

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
        if ($homeScore !== '' && $awayScore !== '') {
            // Validate scores are non-negative integers
            if (!is_numeric($homeScore) || !is_numeric($awayScore) || (int) $homeScore < 0 || (int) $awayScore < 0) {
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

        // Get parameters from POST with fallback to league data
        $startDate = $this->post('startDate', $league['startDate'] ?? date('Y-m-d'));
        $frequency = $this->post('frequency', $league['frequency'] ?? 'weekly');
        $matchTime = $this->post('matchTime', $league['matchTime'] ?? '15:00');

        if (!$startDate) {
            $startDate = date('Y-m-d');
        }

        // Identify existing played fixtures
        $existingFixtures = $league['fixtures'] ?? [];
        $playedFixtures = array_filter($existingFixtures, fn($f) => ($f['result'] ?? null) !== null);
        $playedPairings = array_map(fn($f) => $f['homeTeamId'] . '-' . $f['awayTeamId'], $playedFixtures);

        // Generate full new set of fixtures
        $newFixtures = $this->league->generateFixtures($teamIds, $startDate, $frequency, $matchTime);

        // Filter out new fixtures that match already played pairings
        // (i.e. we only want the unplayed ones from the new generation)
        $unplayedNewFixtures = array_filter($newFixtures, function ($f) use ($playedPairings) {
            $pairing = $f['homeTeamId'] . '-' . $f['awayTeamId'];
            return !in_array($pairing, $playedPairings);
        });

        // Combine played with new unplayed

        // Build map of busy dates for each team from played fixtures
        $busyDates = [];
        foreach ($playedFixtures as $f) {
            $busyDates[$f['homeTeamId']][$f['date']] = true;
            $busyDates[$f['awayTeamId']][$f['date']] = true;
        }

        // Resolving conflicts for unplayed fixtures
        foreach ($unplayedNewFixtures as &$f) {
            $home = $f['homeTeamId'];
            $away = $f['awayTeamId'];
            $date = $f['date'];

            // While either team is busy on this date, move date forward
            // (e.g. if they already played a game on this calculated date)
            // We check against the original busy set mostly, but we should also
            // ensure we don't clash with newly moved fixtures if we wanted to be perfect.
            // But generateFixtures guarantees no self-clashes in the new set.
            // The conflict is only against the played set.
            while (isset($busyDates[$home][$date]) || isset($busyDates[$away][$date])) {
                $date = date('Y-m-d', strtotime($date . ' +1 day'));
            }
            $f['date'] = $date;

            // Mark as busy to prevent subsequent generated matches for these teams 
            // from claiming this shifted slot if the generator was somehow dense, 
            // though standard round-robin usually spaces them out.
            // Adding this safety ensures even dense schedules respect the shift.
            $busyDates[$home][$date] = true;
            $busyDates[$away][$date] = true;
        }
        unset($f); // Break reference

        $finalFixtures = array_merge($playedFixtures, $unplayedNewFixtures);

        // Sort by date/time
        usort($finalFixtures, fn($a, $b) => strcmp($a['date'] . $a['time'], $b['date'] . $b['time']));

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

}
