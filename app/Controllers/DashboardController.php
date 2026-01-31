<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Season;
use App\Models\Team;
use App\Models\League;
use App\Models\Cup;

/**
 * Dashboard controller - the home page after login.
 */
class DashboardController extends Controller
{
    /**
     * Show the dashboard with active season summary.
     */
    public function index(): void
    {
        $seasonModel = new Season();
        $teamModel = new Team();
        $leagueModel = new League();
        $cupModel = new Cup();

        $activeSeason = $seasonModel->getActive();
        $teamCount = $teamModel->count();

        $leagues = [];
        $cups = [];

        if ($activeSeason) {
            $leagues = $leagueModel->getBySeasonId($activeSeason['id']);
            $cups = $cupModel->getBySeasonId($activeSeason['id']);
        }

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'currentPage' => 'dashboard',
            'activeSeason' => $activeSeason,
            'teamCount' => $teamCount,
            'leagues' => $leagues,
            'cups' => $cups,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    /**
     * AJAX endpoint to get upcoming fixtures for a competition.
     */
    public function getUpcomingFixtures(): void
    {
        // Simple authentication check (should be handled by middleware in a real app)
        $type = $_GET['type'] ?? '';
        $id = $_GET['id'] ?? '';

        if (!$type || !$id) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Missing parameters']);
            return;
        }

        $fixtures = [];
        $teamModel = new Team();
        $teams = $teamModel->all(); // Returns array of teams keyed by ID or list? let's check Team model.
        // Assuming getAll returns a list, let's index by ID for easier lookup
        $teamsMap = [];
        foreach ($teams as $t) {
            $teamsMap[$t['id']] = $t;
        }

        if ($type === 'league') {
            $leagueModel = new League();
            $league = $leagueModel->find($id);
            if ($league && !empty($league['fixtures'])) {
                $fixtures = $this->filterUpcoming($league['fixtures']);
            }
        } elseif ($type === 'cup') {
            $cupModel = new Cup();
            $cup = $cupModel->find($id);
            if ($cup && !empty($cup['rounds'])) {
                // Flatten cup rounds into a single list of fixtures
                $allFixtures = [];
                foreach ($cup['rounds'] as $round) {
                    foreach ($round['fixtures'] as $f) {
                        $f['roundName'] = $round['name']; // Add round context
                        $allFixtures[] = $f;
                    }
                }
                $fixtures = $this->filterUpcoming($allFixtures);
            }
        }

        // Enrich fixtures with team details
        foreach ($fixtures as &$fixture) {
            if (isset($teamsMap[$fixture['homeTeamId']])) {
                $fixture['homeTeam'] = $teamsMap[$fixture['homeTeamId']];
            }
            if (isset($teamsMap[$fixture['awayTeamId']])) {
                $fixture['awayTeam'] = $teamsMap[$fixture['awayTeamId']];
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['fixtures' => $fixtures]);
    }

    /**
     * Filter fixtures to find the next "round" (matches on the earliest future/today date).
     */
    private function filterUpcoming(array $fixtures): array
    {
        // 1. Filter out played matches (those with a result)
        $unplayed = array_filter($fixtures, function ($f) {
            return empty($f['result']);
        });

        if (empty($unplayed)) {
            return [];
        }

        // 2. Sort by date and time
        usort($unplayed, function ($a, $b) {
            $dateA = $a['date'] . ' ' . ($a['time'] ?? '00:00');
            $dateB = $b['date'] . ' ' . ($b['time'] ?? '00:00');
            return strcmp($dateA, $dateB);
        });

        // 3. Group by date to find the "next round"
        // In a league, a round might be split over a weekend.
        // But for simplicity, let's take the very first date found and returning all matches on that date.
        // Or if the next matches are very close (e.g. same weekend), return them.
        // Let's stick to the simplest: Get the date of the first unplayed fixture, return all fixtures on that date.

        $firstFixture = reset($unplayed);
        $nextDate = $firstFixture['date'];

        $nextRound = array_filter($unplayed, function ($f) use ($nextDate) {
            return $f['date'] === $nextDate;
        });

        return array_values($nextRound);
    }
}
