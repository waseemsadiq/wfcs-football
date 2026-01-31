<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Cup;
use App\Models\Team;
use App\Models\Season;

/**
 * Controller for managing cup competitions.
 */
class CupsController extends Controller
{
    private Cup $cup;
    private Team $team;
    private Season $season;

    public function __construct()
    {
        parent::__construct();
        $this->cup = new Cup();
        $this->team = new Team();
        $this->season = new Season();
    }

    /**
     * Display all cups.
     */
    public function index(): void
    {
        $cups = $this->cup->all();

        foreach ($cups as &$cup) {
            $season = $this->season->find($cup['seasonId'] ?? '');
            $cup['seasonName'] = $season['name'] ?? 'Unknown';
        }

        $this->render('cups/index', [
            'title' => 'Cups',
            'currentPage' => 'cups',
            'cups' => $cups,
        ]);
    }

    /**
     * Show form to create a new cup.
     */
    public function create(): void
    {
        $teams = $this->team->allSorted();
        $seasons = $this->season->allSorted();

        $this->render('cups/create', [
            'title' => 'Create Cup',
            'currentPage' => 'cups',
            'teams' => $teams,
            'seasons' => $seasons,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Store a new cup.
     */
    public function store(): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/cups/create');
            return;
        }

        $missing = $this->validateRequired(['name', 'seasonId']);
        if (!empty($missing)) {
            $this->flash('error', 'Please fill in all required fields.');
            $this->redirect('/admin/cups/create');
            return;
        }

        // Validate and sanitize inputs
        $name = $this->sanitizeString($this->post('name'), 100);
        $seasonId = $this->post('seasonId');
        $startDate = $this->post('startDate', '');
        $frequency = $this->post('frequency', 'weekly');
        $matchTime = $this->post('matchTime', '15:00');

        if (!$this->validateLength($name, 1, 100)) {
            $this->flash('error', 'Cup name must be between 1 and 100 characters.');
            $this->redirect('/admin/cups/create');
            return;
        }

        if ($startDate && !$this->validateDate($startDate)) {
            $this->flash('error', 'Invalid date format. Please use YYYY-MM-DD.');
            $this->redirect('/admin/cups/create');
            return;
        }

        if (!$this->validateTime($matchTime)) {
            $this->flash('error', 'Invalid time format. Please use HH:MM.');
            $this->redirect('/admin/cups/create');
            return;
        }

        $frequency = $this->normalizeFrequency($frequency);

        $teamIds = $this->post('teamIds', []);
        if (!is_array($teamIds) || count($teamIds) < 2) {
            $this->flash('error', 'Please select at least 2 teams for the cup.');
            $this->redirect('/admin/cups/create');
            return;
        }

        // Generate bracket
        $rounds = $this->cup->generateBracket($teamIds, $startDate, $frequency, $matchTime);

        $cup = $this->cup->create([
            'name' => $name,
            'seasonId' => $seasonId,
            'teamIds' => $teamIds,
            'rounds' => $rounds,
            'startDate' => $startDate,
            'frequency' => $frequency,
            'matchTime' => $matchTime
        ]);

        // Add cup to season
        $this->season->addCup($seasonId, $cup['id']);

        $this->flash('success', 'Cup created with bracket generated.');
        $this->redirect('/admin/cups/' . $cup['slug']);
    }

    /**
     * Display a single cup with bracket.
     */
    public function show(string $slug): void
    {
        $cup = $this->cup->findWhere('slug', $slug);

        if (!$cup) {
            $this->flash('error', 'Cup not found.');
            $this->redirect('/admin/cups');
            return;
        }

        $teams = $this->team->all();
        $rounds = $this->enrichRoundsWithTeamData($cup['rounds'] ?? [], $teams);

        $this->render('cups/show', [
            'title' => $cup['name'],
            'currentPage' => 'cups',
            'cup' => $cup,
            'rounds' => $rounds,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Show form to edit cup details.
     */
    public function edit(string $slug): void
    {
        $cup = $this->cup->findWhere('slug', $slug);

        if (!$cup) {
            $this->flash('error', 'Cup not found.');
            $this->redirect('/admin/cups');
            return;
        }

        $this->render('cups/edit', [
            'title' => 'Edit ' . $cup['name'],
            'currentPage' => 'cups',
            'cup' => $cup,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * Update cup details.
     */
    public function update(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/cups/' . $slug . '/edit');
            return;
        }

        $cup = $this->cup->findWhere('slug', $slug);
        if (!$cup) {
            $this->flash('error', 'Cup not found.');
            $this->redirect('/admin/cups');
            return;
        }

        // Validate and sanitize inputs
        $newName = $this->sanitizeString($this->post('name', $cup['name']), 100);
        $startDate = $this->post('startDate', $cup['startDate'] ?? '');
        $frequency = $this->post('frequency', $cup['frequency'] ?? 'weekly');
        $matchTime = $this->post('matchTime', $cup['matchTime'] ?? '15:00');

        if (!$this->validateLength($newName, 1, 100)) {
            $this->flash('error', 'Cup name must be between 1 and 100 characters.');
            $this->redirect('/admin/cups/' . $slug . '/edit');
            return;
        }

        if ($startDate && !$this->validateDate($startDate)) {
            $this->flash('error', 'Invalid date format. Please use YYYY-MM-DD.');
            $this->redirect('/admin/cups/' . $slug . '/edit');
            return;
        }

        if ($matchTime && !$this->validateTime($matchTime)) {
            $this->flash('error', 'Invalid time format. Please use HH:MM.');
            $this->redirect('/admin/cups/' . $slug . '/edit');
            return;
        }

        $frequency = $this->normalizeFrequency($frequency);

        $this->cup->update($cup['id'], [
            'name' => $newName,
            'startDate' => $startDate,
            'frequency' => $frequency,
            'matchTime' => $matchTime,
        ]);

        $newSlug = Cup::slugify($newName);

        $this->flash('success', 'Cup updated.');
        $this->redirect('/admin/cups/' . $newSlug);
    }

    /**
     * Regenerate cup fixtures.
     */
    public function regenerateFixtures(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission.');
            $this->redirect('/admin/cups/' . $slug . '/fixtures');
            return;
        }

        $cup = $this->cup->findWhere('slug', $slug);
        if (!$cup) {
            $this->flash('error', 'Cup not found.');
            $this->redirect('/admin/cups');
            return;
        }

        $startDate = $this->post('startDate', $cup['startDate'] ?? date('Y-m-d'));
        $frequency = $this->post('frequency', $cup['frequency'] ?? 'weekly');
        $matchTime = $this->post('matchTime', $cup['matchTime'] ?? '15:00');

        // Check if any results already exist
        $hasResults = false;
        foreach ($cup['rounds'] ?? [] as $round) {
            foreach ($round['fixtures'] as $fixture) {
                if (($fixture['result'] ?? null) !== null) {
                    $hasResults = true;
                    break 2;
                }
            }
        }

        if ($hasResults) {
            // Only reschedule unplayed fixtures to avoid breaking the bracket
            $this->cup->rescheduleUnplayed($cup['id'], $startDate, $frequency, $matchTime);
            $message = 'Unplayed fixtures rescheduled successfully.';
        } else {
            // Full regeneration (re-drawing bracket)
            $rounds = $this->cup->generateBracket($cup['teamIds'], $startDate, $frequency, $matchTime);
            $this->cup->update($cup['id'], [
                'rounds' => $rounds,
            ]);
            $message = 'Bracket regenerated successfully.';
        }

        // Always update settings
        $this->cup->update($cup['id'], [
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
                'message' => $message
            ]);
        }

        $this->flash('success', $message);
        $this->redirect('/admin/cups/' . $slug . '/fixtures');
    }

    /**
     * Delete a cup.
     */
    public function delete(string $slug): void
    {
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            $this->redirect('/admin/cups');
            return;
        }

        $cup = $this->cup->findWhere('slug', $slug);
        if (!$cup) {
            $this->flash('error', 'Cup not found.');
            $this->redirect('/admin/cups');
            return;
        }

        $this->cup->delete($cup['id']);
        $this->flash('success', 'Cup deleted.');
        $this->redirect('/admin/cups');
    }

    /**
     * Show fixtures page for editing results.
     */
    public function fixtures(string $slug): void
    {
        $cup = $this->cup->findWhere('slug', $slug);

        if (!$cup) {
            $this->flash('error', 'Cup not found.');
            $this->redirect('/admin/cups');
            return;
        }

        $teams = $this->team->all();
        $rounds = $this->enrichRoundsWithTeamData($cup['rounds'] ?? [], $teams);

        $this->render('cups/fixtures', [
            'title' => $cup['name'] . ' - Fixtures',
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

        $cup = $this->cup->findWhere('slug', $slug);
        if (!$cup) {
            $this->flash('error', 'Cup not found.');
            $this->redirect('/admin/cups');
            return;
        }

        $id = $cup['id'];

        $fixtureId = $this->post('fixtureId');
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

        // Update date/time if provided
        if ($date && $time) {
            $this->cup->updateFixtureDateTime($id, $fixtureId, $date, $time);
        }

        // Update result if scores provided
        $homeScore = $this->post('homeScore');
        $awayScore = $this->post('awayScore');

        if ($homeScore !== null && $homeScore !== '' && $awayScore !== null && $awayScore !== '') {
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

            // Optional fields with proper null handling
            $homeScoreET = $this->post('homeScoreET');
            $awayScoreET = $this->post('awayScoreET');
            $homePens = $this->post('homePens');
            $awayPens = $this->post('awayPens');

            // Validate ET scores if provided
            if (($homeScoreET !== null && $homeScoreET !== '') || ($awayScoreET !== null && $awayScoreET !== '')) {
                if (($homeScoreET !== null && $homeScoreET !== '' && (!is_numeric($homeScoreET) || (int) $homeScoreET < 0)) ||
                    ($awayScoreET !== null && $awayScoreET !== '' && (!is_numeric($awayScoreET) || (int) $awayScoreET < 0))) {
                    if ($this->isAjaxRequest()) {
                        $this->json(['success' => false, 'error' => 'Extra time scores must be non-negative numbers.']);
                        return;
                    }
                    $this->flash('error', 'Extra time scores must be non-negative numbers.');
                    $this->redirect('/admin/cups/' . $slug . '/fixtures');
                    return;
                }
            }

            // Validate penalty scores if provided
            if (($homePens !== null && $homePens !== '') || ($awayPens !== null && $awayPens !== '')) {
                if (($homePens !== null && $homePens !== '' && (!is_numeric($homePens) || (int) $homePens < 0)) ||
                    ($awayPens !== null && $awayPens !== '' && (!is_numeric($awayPens) || (int) $awayPens < 0))) {
                    if ($this->isAjaxRequest()) {
                        $this->json(['success' => false, 'error' => 'Penalty scores must be non-negative numbers.']);
                        return;
                    }
                    $this->flash('error', 'Penalty scores must be non-negative numbers.');
                    $this->redirect('/admin/cups/' . $slug . '/fixtures');
                    return;
                }
            }

            // Auto-detect extra time from scores
            $hasETScores = ($homeScoreET !== null && $homeScoreET !== '') ||
                           ($awayScoreET !== null && $awayScoreET !== '');

            // Auto-detect penalties from scores
            $hasPenScores = ($homePens !== null && $homePens !== '') ||
                            ($awayPens !== null && $awayPens !== '');

            $result = [
                'homeScore' => (int) $homeScore,
                'awayScore' => (int) $awayScore,
                'homeScorers' => $this->sanitizeString($this->post('homeScorers', ''), 500),
                'awayScorers' => $this->sanitizeString($this->post('awayScorers', ''), 500),
                'homeCards' => $this->sanitizeString($this->post('homeCards', ''), 500),
                'awayCards' => $this->sanitizeString($this->post('awayCards', ''), 500),
                'extraTime' => $hasETScores || $this->post('extraTime') === '1',
                'homeScoreET' => ($homeScoreET !== null && $homeScoreET !== '') ? (int) $homeScoreET : null,
                'awayScoreET' => ($awayScoreET !== null && $awayScoreET !== '') ? (int) $awayScoreET : null,
                'penalties' => $hasPenScores || $this->post('penalties') === '1',
                'homePens' => ($homePens !== null && $homePens !== '') ? (int) $homePens : null,
                'awayPens' => ($awayPens !== null && $awayPens !== '') ? (int) $awayPens : null,
            ];

            $result['winnerId'] = $this->determineWinner($result);
            $this->cup->updateFixtureResult($id, $fixtureId, $result);
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
     * Enrich cup rounds with team names and colours.
     */
    private function enrichRoundsWithTeamData(array $rounds, array $teams): array
    {
        $enrichedRounds = [];

        foreach ($rounds as $round) {
            $enrichedFixtures = [];
            foreach ($round['fixtures'] as $fixture) {
                $homeTeam = $this->findById($teams, $fixture['homeTeamId'] ?? '');
                $awayTeam = $this->findById($teams, $fixture['awayTeamId'] ?? '');
                $fixture['homeTeamName'] = $homeTeam['name'] ?? 'TBD';
                $fixture['awayTeamName'] = $awayTeam['name'] ?? 'TBD';
                $fixture['homeTeamColour'] = $homeTeam['colour'] ?? '#ccc';
                $fixture['awayTeamColour'] = $awayTeam['colour'] ?? '#ccc';
                $enrichedFixtures[] = $fixture;
            }
            $enrichedRounds[] = [
                'name' => $round['name'],
                'fixtures' => $enrichedFixtures,
            ];
        }

        return $enrichedRounds;
    }

    /**
     * Determine the winner of a cup fixture based on result data.
     * Returns 'home', 'away', or null for a draw.
     */
    private function determineWinner(array $result): ?string
    {
        // Check penalties first (highest priority)
        if ($result['penalties'] && $result['homePens'] !== null && $result['awayPens'] !== null) {
            return $this->compareScores($result['homePens'], $result['awayPens']);
        }

        // Check extra time next
        if ($result['extraTime'] && $result['homeScoreET'] !== null && $result['awayScoreET'] !== null) {
            return $this->compareScores($result['homeScoreET'], $result['awayScoreET']);
        }

        // Fall back to regular time score
        return $this->compareScores($result['homeScore'], $result['awayScore']);
    }

    /**
     * Compare two scores and return winner designation.
     */
    private function compareScores(int $homeScore, int $awayScore): ?string
    {
        if ($homeScore > $awayScore) {
            return 'home';
        }
        if ($awayScore > $homeScore) {
            return 'away';
        }
        return null;
    }
}
