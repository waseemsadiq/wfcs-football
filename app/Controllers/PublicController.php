<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Season;
use App\Models\Team;
use App\Models\League;
use App\Models\Cup;

/**
 * Public controller for the public-facing website.
 * Shows league standings, cup brackets, team info, and fixtures.
 */
class PublicController extends Controller
{
    private Season $seasonModel;
    private Team $teamModel;
    private League $leagueModel;
    private Cup $cupModel;

    public function __construct()
    {
        parent::__construct();
        $this->seasonModel = new Season();
        $this->teamModel = new Team();
        $this->leagueModel = new League();
        $this->cupModel = new Cup();
    }

    /**
     * Homepage - shows active leagues, cups, recent results, and upcoming fixtures.
     */
    public function index(): void
    {
        $activeSeason = $this->seasonModel->getActive();
        $teams = $this->teamModel->all();
        $teamsById = $this->indexById($teams);

        $leagues = [];
        $cups = [];
        $recentResults = [];
        $upcomingFixtures = [];

        if ($activeSeason) {
            $leagues = $this->leagueModel->getBySeasonId($activeSeason['id']);
            $cups = $this->cupModel->getBySeasonId($activeSeason['id']);

            // Gather all fixtures from leagues and cups
            $allFixtures = $this->gatherAllFixtures($leagues, $cups, $teamsById);

            // Split into completed and upcoming
            $recentResults = $this->getRecentResults($allFixtures, 5);
            $upcomingFixtures = $this->getUpcomingFixtures($allFixtures, 5);
        }

        $this->render('public/index', [
            'title' => 'Home',
            'seasonName' => $activeSeason['name'] ?? null,
            'leagues' => $leagues,
            'cups' => $cups,
            'recentResults' => $recentResults,
            'upcomingFixtures' => $upcomingFixtures,
        ], 'public');
    }

    /**
     * League page - shows standings and all fixtures.
     */
    public function league(string $slug): void
    {
        $league = $this->leagueModel->findWhere('slug', $slug);

        if (!$league) {
            $this->handleNotFound('League not found');
            return;
        }

        $teams = $this->teamModel->all();
        $teamsById = $this->indexById($teams);

        $standings = $this->leagueModel->calculateStandings($league, $teams);
        $fixtures = $this->enrichFixtures($league['fixtures'] ?? [], $teamsById, $league['name'], 'league');

        $activeSeason = $this->seasonModel->getActive();

        $this->render('public/league', [
            'title' => $league['name'],
            'seasonName' => $activeSeason['name'] ?? null,
            'league' => $league,
            'standings' => $standings,
            'fixtures' => $fixtures,
        ], 'public');
    }

    /**
     * Cup page - shows bracket and fixtures.
     */
    public function cup(string $slug): void
    {
        $cup = $this->cupModel->findWhere('slug', $slug);

        if (!$cup) {
            $this->handleNotFound('Cup not found');
            return;
        }

        $teams = $this->teamModel->all();
        $teamsById = $this->indexById($teams);

        // Enrich rounds with team data
        $rounds = [];
        foreach ($cup['rounds'] ?? [] as $round) {
            $enrichedFixtures = $this->enrichFixtures(
                $round['fixtures'] ?? [],
                $teamsById,
                $cup['name'],
                'cup',
                $round['name']
            );
            $rounds[] = [
                'name' => $round['name'],
                'fixtures' => $enrichedFixtures,
            ];
        }

        $activeSeason = $this->seasonModel->getActive();

        $this->render('public/cup', [
            'title' => $cup['name'],
            'seasonName' => $activeSeason['name'] ?? null,
            'cup' => $cup,
            'rounds' => $rounds,
        ], 'public');
    }

    /**
     * Team page - shows team info and all fixtures.
     */
    public function team(string $slug): void
    {
        $team = $this->teamModel->findWhere('slug', $slug);

        if (!$team) {
            $this->handleNotFound('Team not found');
            return;
        }

        $id = $team['id'];

        $activeSeason = $this->seasonModel->getActive();
        $teamsById = $this->indexById($this->teamModel->all());

        $fixtures = [];

        if ($activeSeason) {
            $leagues = $this->leagueModel->getBySeasonId($activeSeason['id']);
            $cups = $this->cupModel->getBySeasonId($activeSeason['id']);

            // Gather fixtures where this team plays
            $allFixtures = $this->gatherAllFixtures($leagues, $cups, $teamsById);
            $fixtures = array_filter($allFixtures, function ($f) use ($id) {
                return ($f['homeTeamId'] ?? '') === $id || ($f['awayTeamId'] ?? '') === $id;
            });

            // Sort by date
            usort($fixtures, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));
            // Identify participating competitions
            $competitions = [];
            $allTeams = array_values($teamsById);

            foreach ($leagues as $league) {
                if (in_array($id, $league['teamIds'] ?? [])) {
                    // Calculate position
                    $standings = $this->leagueModel->calculateStandings($league, $allTeams);
                    $position = '-';
                    foreach ($standings as $idx => $row) {
                        if ($row['teamId'] === $id) {
                            $pos = $idx + 1;
                            $suffix = 'th';
                            if (!in_array(($pos % 100), [11, 12, 13])) {
                                switch ($pos % 10) {
                                    case 1:
                                        $suffix = 'st';
                                        break;
                                    case 2:
                                        $suffix = 'nd';
                                        break;
                                    case 3:
                                        $suffix = 'rd';
                                        break;
                                }
                            }
                            $position = $pos . $suffix;
                            break;
                        }
                    }

                    $competitions[] = [
                        'name' => $league['name'],
                        'slug' => $league['slug'],
                        'type' => 'league',
                        'detail' => $position,
                        'url' => '/league/' . $league['slug']
                    ];
                }
            }

            foreach ($cups as $cup) {
                if (in_array($id, $cup['teamIds'] ?? [])) {
                    // Determine status
                    $status = 'Participant';
                    foreach ($cup['rounds'] ?? [] as $round) {
                        foreach ($round['fixtures'] ?? [] as $f) {
                            if (($f['homeTeamId'] ?? '') === $id || ($f['awayTeamId'] ?? '') === $id) {
                                // Found fixture in this round
                                if (!isset($f['result'])) {
                                    $status = $round['name']; // Upcoming match in this round
                                } else {
                                    // Match played
                                    $home = (int) ($f['result']['homeScore'] ?? 0);
                                    $away = (int) ($f['result']['awayScore'] ?? 0);
                                    $isHome = ($f['homeTeamId'] ?? '') === $id;

                                    $won = false;
                                    if ($home > $away)
                                        $won = $isHome;
                                    elseif ($away > $home)
                                        $won = !$isHome;
                                    else {
                                        // Draw - check penalties if exist
                                        if (isset($f['result']['penalties'])) {
                                            $pHome = (int) ($f['result']['penalties']['homeScore'] ?? 0);
                                            $pAway = (int) ($f['result']['penalties']['awayScore'] ?? 0);
                                            $won = $isHome ? ($pHome > $pAway) : ($pAway > $pHome);
                                        }
                                    }

                                    if (!$won) {
                                        $status = 'Knocked out in ' . $round['name'];
                                    } else {
                                        // Won this round
                                        $status = 'Won ' . $round['name'];
                                        if (stripos($round['name'], 'Final') !== false) {
                                            $status = 'Winner';
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $competitions[] = [
                        'name' => $cup['name'],
                        'slug' => $cup['slug'],
                        'type' => 'cup',
                        'detail' => $status,
                        'url' => '/cup/' . $cup['slug']
                    ];
                }
            }
        }

        $this->render('public/team', [
            'title' => $team['name'],
            'seasonName' => $activeSeason['name'] ?? null,
            'team' => $team,
            'competitions' => $competitions ?? [],
            'fixtures' => array_values($fixtures),
        ], 'public');
    }

    /**
     * Leagues overview page - shows all leagues with interactive selector.
     */
    public function leagues(): void
    {
        $activeSeason = $this->seasonModel->getActive();
        $leagues = [];

        if ($activeSeason) {
            $leagues = $this->leagueModel->getBySeasonId($activeSeason['id']);

            // Sort leagues alphabetically by name
            usort($leagues, fn($a, $b) => strcmp($a['name'] ?? '', $b['name'] ?? ''));
        }

        $this->render('public/leagues', [
            'title' => 'Leagues',
            'seasonName' => $activeSeason['name'] ?? null,
            'leagues' => $leagues,
        ], 'public');
    }

    /**
     * Cups overview page - shows all cups with interactive selector.
     */
    public function cups(): void
    {
        $activeSeason = $this->seasonModel->getActive();
        $cups = [];

        if ($activeSeason) {
            $cups = $this->cupModel->getBySeasonId($activeSeason['id']);

            // Sort cups alphabetically by name
            usort($cups, fn($a, $b) => strcmp($a['name'] ?? '', $b['name'] ?? ''));
        }

        $this->render('public/cups', [
            'title' => 'Cups',
            'seasonName' => $activeSeason['name'] ?? null,
            'cups' => $cups,
        ], 'public');
    }

    /**
     * AJAX endpoint - returns cup data as JSON.
     */
    public function cupData(string $slug): void
    {
        header('Content-Type: application/json');

        $cup = $this->cupModel->findWhere('slug', $slug);

        if (!$cup) {
            http_response_code(404);
            echo json_encode(['error' => 'Cup not found']);
            return;
        }

        $teams = $this->teamModel->all();
        $teamsById = $this->indexById($teams);

        // Enrich rounds with team data
        $rounds = [];
        foreach ($cup['rounds'] ?? [] as $round) {
            $enrichedFixtures = $this->enrichFixtures(
                $round['fixtures'] ?? [],
                $teamsById,
                $cup['name'],
                'cup',
                $round['name']
            );
            $rounds[] = [
                'name' => $round['name'],
                'fixtures' => $enrichedFixtures,
            ];
        }

        echo json_encode([
            'cup' => [
                'name' => $cup['name'],
                'slug' => $cup['slug'],
            ],
            'rounds' => $rounds,
        ]);
    }

    /**
     * Teams overview page - shows all teams with interactive selector.
     */
    public function teams(): void
    {
        $activeSeason = $this->seasonModel->getActive();
        $teams = $this->teamModel->all();

        // Sort teams alphabetically
        usort($teams, fn($a, $b) => strcmp($a['name'] ?? '', $b['name'] ?? ''));

        $this->render('public/teams', [
            'title' => 'Teams',
            'seasonName' => $activeSeason['name'] ?? null,
            'teams' => $teams,
        ], 'public');
    }

    /**
     * AJAX endpoint - returns team data as JSON.
     */
    public function teamData(string $slug): void
    {
        header('Content-Type: application/json');

        $team = $this->teamModel->findWhere('slug', $slug);

        if (!$team) {
            http_response_code(404);
            echo json_encode(['error' => 'Team not found']);
            return;
        }

        $id = $team['id'];
        $activeSeason = $this->seasonModel->getActive();
        $teamsById = $this->indexById($this->teamModel->all());

        $fixtures = [];
        $competitions = [];

        if ($activeSeason) {
            $leagues = $this->leagueModel->getBySeasonId($activeSeason['id']);
            $cups = $this->cupModel->getBySeasonId($activeSeason['id']);

            // Gather fixtures where this team plays
            $allFixtures = $this->gatherAllFixtures($leagues, $cups, $teamsById);
            $teamFixtures = array_filter($allFixtures, function ($f) use ($id) {
                return ($f['homeTeamId'] ?? '') === $id || ($f['awayTeamId'] ?? '') === $id;
            });

            // Sort by date
            usort($teamFixtures, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));

            $fixtures = array_values($teamFixtures);

            // Identify participating competitions
            $allTeams = array_values($teamsById);

            foreach ($leagues as $league) {
                if (in_array($id, $league['teamIds'] ?? [])) {
                    // Calculate position
                    $standings = $this->leagueModel->calculateStandings($league, $allTeams);
                    $position = '-';
                    foreach ($standings as $idx => $row) {
                        if ($row['teamId'] === $id) {
                            $pos = $idx + 1;
                            $suffix = 'th';
                            if (!in_array(($pos % 100), [11, 12, 13])) {
                                switch ($pos % 10) {
                                    case 1:
                                        $suffix = 'st';
                                        break;
                                    case 2:
                                        $suffix = 'nd';
                                        break;
                                    case 3:
                                        $suffix = 'rd';
                                        break;
                                }
                            }
                            $position = $pos . $suffix;
                            break;
                        }
                    }

                    $competitions[] = [
                        'name' => $league['name'],
                        'slug' => $league['slug'],
                        'type' => 'league',
                        'detail' => $position,
                        'url' => '/league/' . $league['slug']
                    ];
                }
            }

            foreach ($cups as $cup) {
                if (in_array($id, $cup['teamIds'] ?? [])) {
                    $status = 'Participant';
                    foreach ($cup['rounds'] ?? [] as $round) {
                        foreach ($round['fixtures'] ?? [] as $f) {
                            if (($f['homeTeamId'] ?? '') === $id || ($f['awayTeamId'] ?? '') === $id) {
                                if (!isset($f['result'])) {
                                    $status = $round['name'];
                                } else {
                                    $home = (int) ($f['result']['homeScore'] ?? 0);
                                    $away = (int) ($f['result']['awayScore'] ?? 0);
                                    $isHome = ($f['homeTeamId'] ?? '') === $id;

                                    $won = false;
                                    if ($home > $away)
                                        $won = $isHome;
                                    elseif ($away > $home)
                                        $won = !$isHome;
                                    else {
                                        if (isset($f['result']['penalties'])) {
                                            $pHome = (int) ($f['result']['penalties']['homeScore'] ?? 0);
                                            $pAway = (int) ($f['result']['penalties']['awayScore'] ?? 0);
                                            $won = $isHome ? ($pHome > $pAway) : ($pAway > $pHome);
                                        }
                                    }

                                    if (!$won) {
                                        $status = 'Knocked out in ' . $round['name'];
                                    } else {
                                        $status = 'Won ' . $round['name'];
                                        if (stripos($round['name'], 'Final') !== false) {
                                            $status = 'Winner';
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $competitions[] = [
                        'name' => $cup['name'],
                        'slug' => $cup['slug'],
                        'type' => 'cup',
                        'detail' => $status,
                        'url' => '/cup/' . $cup['slug']
                    ];
                }
            }
        }

        // Split fixtures into recent and upcoming
        $recentResults = $this->getRecentResults($fixtures, 5);
        $upcomingFixtures = $this->getUpcomingFixtures($fixtures, 100);

        echo json_encode([
            'team' => $team,
            'competitions' => $competitions,
            'recentResults' => $recentResults,
            'upcomingFixtures' => $upcomingFixtures,
        ]);
    }

    /**
     * AJAX endpoint - returns league data as JSON.
     */
    public function leagueData(string $slug): void
    {
        header('Content-Type: application/json');

        $league = $this->leagueModel->findWhere('slug', $slug);

        if (!$league) {
            http_response_code(404);
            echo json_encode(['error' => 'League not found']);
            return;
        }

        $teams = $this->teamModel->all();
        $teamsById = $this->indexById($teams);

        // Calculate standings
        $standings = $this->leagueModel->calculateStandings($league, $teams);

        // Get fixtures
        $fixtures = $this->enrichFixtures($league['fixtures'] ?? [], $teamsById, $league['name'], 'league');

        // Recent results: last 3 fixture dates
        $recentResults = $this->getRecentResultsByDates($fixtures, 3);

        // Upcoming fixtures: all future matches
        $upcomingFixtures = $this->getUpcomingFixtures($fixtures, 1000);

        echo json_encode([
            'league' => [
                'name' => $league['name'],
                'slug' => $league['slug'],
            ],
            'standings' => $standings,
            'recentResults' => $recentResults,
            'upcomingFixtures' => $upcomingFixtures,
        ]);
    }

    /**
     * Get recent results from the last N fixture dates.
     */
    private function getRecentResultsByDates(array $fixtures, int $numDates): array
    {
        // Filter completed fixtures
        $completed = array_filter($fixtures, fn($f) => isset($f['result']) && $f['result'] !== null);

        if (empty($completed)) {
            return [];
        }

        // Extract unique dates
        $dates = array_unique(array_map(fn($f) => $f['date'] ?? '', $completed));

        // Sort dates descending (newest first)
        rsort($dates);

        // Take first N dates
        $recentDates = array_slice($dates, 0, $numDates);

        // Get all fixtures from those dates
        $result = array_filter($completed, fn($f) => in_array($f['date'] ?? '', $recentDates));

        // Sort by date ascending (oldest first)
        usort($result, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));

        return array_values($result);
    }

    /**
     * Gather all fixtures from leagues and cups.
     */
    private function gatherAllFixtures(array $leagues, array $cups, array $teamsById): array
    {
        $allFixtures = [];

        foreach ($leagues as $league) {
            $fixtures = $this->enrichFixtures(
                $league['fixtures'] ?? [],
                $teamsById,
                $league['name'],
                'league'
            );
            $allFixtures = array_merge($allFixtures, $fixtures);
        }

        foreach ($cups as $cup) {
            foreach ($cup['rounds'] ?? [] as $round) {
                $fixtures = $this->enrichFixtures(
                    $round['fixtures'] ?? [],
                    $teamsById,
                    $cup['name'],
                    'cup',
                    $round['name']
                );
                $allFixtures = array_merge($allFixtures, $fixtures);
            }
        }

        return $allFixtures;
    }

    /**
     * Enrich fixtures with team data.
     */
    private function enrichFixtures(
        array $fixtures,
        array $teamsById,
        string $competitionName,
        string $competitionType,
        ?string $roundName = null
    ): array {
        $enriched = [];

        foreach ($fixtures as $fixture) {
            $homeTeam = $teamsById[$fixture['homeTeamId'] ?? ''] ?? null;
            $awayTeam = $teamsById[$fixture['awayTeamId'] ?? ''] ?? null;

            $enriched[] = array_merge($fixture, [
                'homeTeam' => $homeTeam,
                'awayTeam' => $awayTeam,
                'competitionName' => $competitionName,
                'competitionType' => $competitionType,
                'roundName' => $roundName,
            ]);
        }

        return $enriched;
    }

    /**
     * Get recent completed fixtures.
     */
    private function getRecentResults(array $fixtures, int $limit): array
    {
        $completed = array_filter($fixtures, fn($f) => isset($f['result']) && $f['result'] !== null);

        usort($completed, function ($a, $b) {
            return strcmp($b['date'] ?? '', $a['date'] ?? '');
        });

        return array_slice($completed, 0, $limit);
    }

    /**
     * Get upcoming fixtures.
     */
    private function getUpcomingFixtures(array $fixtures, int $limit): array
    {
        $today = date('Y-m-d');

        $upcoming = array_filter($fixtures, function ($f) use ($today) {
            $hasNoResult = !isset($f['result']) || $f['result'] === null;
            $hasDate = !empty($f['date']);
            $isFuture = ($f['date'] ?? '') >= $today;
            $hasTeams = !empty($f['homeTeamId']) && !empty($f['awayTeamId']);
            return $hasNoResult && $hasDate && $isFuture && $hasTeams;
        });

        usort($upcoming, function ($a, $b) {
            return strcmp($a['date'] ?? '', $b['date'] ?? '');
        });

        return array_slice($upcoming, 0, $limit);
    }

    /**
     * Index an array by ID.
     */
    private function indexById(array $items): array
    {
        $indexed = [];
        foreach ($items as $item) {
            if (isset($item['id'])) {
                $indexed[$item['id']] = $item;
            }
        }
        return $indexed;
    }

    /**
     * Handle 404 not found.
     */
    private function handleNotFound(string $message): void
    {
        http_response_code(404);
        $activeSeason = $this->seasonModel->getActive();

        $this->render('public/not-found', [
            'title' => 'Not Found',
            'seasonName' => $activeSeason['name'] ?? null,
            'message' => $message,
        ], 'public');
    }
}
