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
            'currentPage' => 'home',
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

        $standings = $this->leagueModel->calculateStandings($league['id'], $teams);
        $fixtures = $this->enrichFixtures($league['fixtures'] ?? [], $teamsById, $league['name'], 'league', null, $league['slug'] ?? '', $league['id'] ?? null);

        $activeSeason = $this->seasonModel->getActive();

        $this->render('public/league', [
            'title' => $league['name'],
            'seasonName' => $activeSeason['name'] ?? null,
            'league' => $league,
            'standings' => $standings,
            'fixtures' => $fixtures,
            'currentPage' => 'leagues',
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
                $round['name'],
                $cup['slug'] ?? '',
                $cup['id'] ?? null
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
            'currentPage' => 'cups',
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
                return ($f['homeTeamId'] ?? null) == $id || ($f['awayTeamId'] ?? null) == $id;
            });

            // Sort by date
            usort($fixtures, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));
            // Identify participating competitions
            $competitions = [];
            $allTeams = array_values($teamsById);

            $competitions = $this->buildTeamCompetitions($id, $leagues, $cups, $allTeams);
        }

        $this->render('public/team', [
            'title' => $team['name'],
            'seasonName' => $activeSeason['name'] ?? null,
            'team' => $team,
            'competitions' => $competitions ?? [],
            'fixtures' => array_values($fixtures),
            'currentPage' => 'teams',
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
            'currentPage' => 'leagues',
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
            'currentPage' => 'cups',
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
                $round['name'],
                $cup['slug'] ?? '',
                $cup['id'] ?? null
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
            'currentPage' => 'teams',
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
                return ($f['homeTeamId'] ?? null) == $id || ($f['awayTeamId'] ?? null) == $id;
            });

            // Sort by date
            usort($teamFixtures, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));

            $fixtures = array_values($teamFixtures);

            $allTeams = array_values($teamsById);
            $competitions = $this->buildTeamCompetitions($id, $leagues, $cups, $allTeams);
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
        $standings = $this->leagueModel->calculateStandings($league['id'], $teams);

        // Get fixtures
        $fixtures = $this->enrichFixtures($league['fixtures'] ?? [], $teamsById, $league['name'], 'league', null, $league['slug'] ?? '', $league['id'] ?? null);

        // Render standings using shared partial
        $standingsHtml = $this->renderStandingsHtml($standings);

        // Recent results: last 3 fixture dates
        $recentResults = $this->getRecentResultsByDates($fixtures, 3);
        $recentResultsHtml = $this->renderFixturesHtml($recentResults, true);

        // Upcoming fixtures: all future matches
        $upcomingFixtures = $this->getUpcomingFixtures($fixtures, 1000);
        $upcomingFixturesHtml = $this->renderFixturesHtml($upcomingFixtures, false);

        echo json_encode([
            'league' => [
                'name' => $league['name'],
                'slug' => $league['slug'],
            ],
            'standingsHtml' => $standingsHtml,
            'recentResultsHtml' => $recentResultsHtml,
            'upcomingFixturesHtml' => $upcomingFixturesHtml,
        ]);
    }

    /**
     * Render fixtures to HTML using the shared partial.
     */
    private function renderFixturesHtml(array $fixtures, bool $showResult): string
    {
        if (empty($fixtures)) {
            return '<div class="text-center py-12 text-text-muted"><p>No fixtures</p></div>';
        }

        // Calculate base path for subfolder installations
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = rtrim(dirname($scriptName), '/\\');
        $basePath = str_replace('\\', '/', $basePath); // Windows compat
        $basePath = ($basePath !== '' && $basePath !== '/') ? $basePath : '';

        ob_start();

        // Group fixtures by date (for both recent results and upcoming fixtures)
        $groupedFixtures = [];
        $dates = [];

        foreach ($fixtures as $fixture) {
            $date = $fixture['date'] ?? 'TBD';
            if (!isset($groupedFixtures[$date])) {
                $groupedFixtures[$date] = [];
                $dates[] = $date;
            }
            $groupedFixtures[$date][] = $fixture;
        }

        // Sort dates based on whether showing results (descending) or upcoming (ascending)
        usort($dates, function ($a, $b) use ($showResult) {
            if ($a === 'TBD')
                return 1;
            if ($b === 'TBD')
                return -1;

            $comparison = strtotime($a) - strtotime($b);

            // For recent results, show newest first (descending)
            // For upcoming, show soonest first (ascending)
            return $showResult ? -$comparison : $comparison;
        });

        echo '<div class="flex flex-col">';
        foreach ($dates as $date) {
            echo '<div class="bg-surface border-l-4 border-l-primary border-b border-border py-2 text-center">';
            echo '<span class="text-xs font-bold text-text-muted uppercase tracking-wider">';
            echo $date !== 'TBD' ? date('D j M', strtotime($date)) : 'TBD';
            echo '</span></div>';
            echo '<ul class="divide-y divide-border border-b border-border last:border-0">';

            foreach ($groupedFixtures[$date] as $fixture) {
                $showDate = false;
                $showCompetition = false;
                include BASE_PATH . '/app/Views/partials/public_fixture.php';
            }

            echo '</ul>';
        }
        echo '</div>';

        return ob_get_clean();
    }

    /**
     * Render standings to HTML using the shared partial.
     */
    private function renderStandingsHtml(array $standings): string
    {
        // Calculate base path for subfolder installations
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = rtrim(dirname($scriptName), '/\\');
        $basePath = str_replace('\\', '/', $basePath); // Windows compat
        $basePath = ($basePath !== '' && $basePath !== '/') ? $basePath : '';

        ob_start();

        // Set context for the partial
        $context = 'public';
        include BASE_PATH . '/app/Views/partials/standings_table.php';

        return ob_get_clean();
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
                'league',
                null,
                $league['slug'] ?? '',
                $league['id'] ?? null
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
                    $round['name'],
                    $cup['slug'] ?? '',
                    $cup['id'] ?? null
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
        ?string $roundName = null,
        string $competitionSlug = '',
        ?int $competitionId = null
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
                'competitionSlug' => $competitionSlug,
                'roundName' => $roundName,
                'leagueId' => $competitionType === 'league' ? $competitionId : null,
                'cupId' => $competitionType === 'cup' ? $competitionId : null,
            ]);
        }

        return $enriched;
    }

    /**
     * Get recent completed fixtures.
     */
    private function getRecentResults(array $fixtures, int $limit): array
    {
        $completed = array_filter($fixtures, fn($f) => !empty($f['result']));

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
            $hasNoResult = empty($f['result']);
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

    /**
     * Build competition data for a team across leagues and cups.
     */
    private function buildTeamCompetitions(int|string $teamId, array $leagues, array $cups, array $allTeams): array
    {
        $competitions = [];

        foreach ($leagues as $league) {
            if (!in_array($teamId, $league['teamIds'] ?? [])) {
                continue;
            }

            $standings = $this->leagueModel->calculateStandings($league['id'], $allTeams);
            $position = $this->getTeamPosition($teamId, $standings);

            $competitions[] = [
                'name' => $league['name'],
                'slug' => $league['slug'],
                'type' => 'league',
                'detail' => $position,
                'url' => '/league/' . $league['slug']
            ];
        }

        foreach ($cups as $cup) {
            if (!in_array($teamId, $cup['teamIds'] ?? [])) {
                continue;
            }

            $status = $this->getTeamCupStatus($teamId, $cup);

            $competitions[] = [
                'name' => $cup['name'],
                'slug' => $cup['slug'],
                'type' => 'cup',
                'detail' => $status,
                'url' => '/cup/' . $cup['slug']
            ];
        }

        return $competitions;
    }

    /**
     * Get a team's position in league standings with ordinal suffix.
     */
    private function getTeamPosition(int|string $teamId, array $standings): string
    {
        foreach ($standings as $idx => $row) {
            if ($row['teamId'] == $teamId) {
                return $this->ordinal($idx + 1);
            }
        }
        return '-';
    }

    /**
     * Get a team's status in a cup competition.
     */
    private function getTeamCupStatus(int|string $teamId, array $cup): string
    {
        $status = 'Participant';

        foreach ($cup['rounds'] ?? [] as $round) {
            foreach ($round['fixtures'] ?? [] as $f) {
                $isHome = ($f['homeTeamId'] ?? null) == $teamId;
                $isAway = ($f['awayTeamId'] ?? null) == $teamId;

                if (!$isHome && !$isAway) {
                    continue;
                }

                if (!isset($f['result'])) {
                    $status = $round['name'];
                    continue;
                }

                $won = $this->didTeamWin($f, $isHome);

                if (!$won) {
                    $status = 'Knocked out in ' . $round['name'];
                } elseif (stripos($round['name'], 'Final') !== false) {
                    $status = 'Winner';
                } else {
                    $status = 'Won ' . $round['name'];
                }
            }
        }

        return $status;
    }

    /**
     * Determine if a team won a cup fixture.
     */
    private function didTeamWin(array $fixture, bool $isHome): bool
    {
        $result = $fixture['result'];
        $home = (int) ($result['homeScore'] ?? 0);
        $away = (int) ($result['awayScore'] ?? 0);

        if ($home > $away) {
            return $isHome;
        }
        if ($away > $home) {
            return !$isHome;
        }

        // Draw - check penalties
        if (isset($result['penalties']) && $result['penalties']) {
            $pHome = (int) ($result['homePens'] ?? 0);
            $pAway = (int) ($result['awayPens'] ?? 0);
            return $isHome ? ($pHome > $pAway) : ($pAway > $pHome);
        }

        return false;
    }

    /**
     * Convert a number to its ordinal form (1st, 2nd, 3rd, etc.).
     */
    private function ordinal(int $number): string
    {
        $suffix = 'th';

        if (!in_array(($number % 100), [11, 12, 13])) {
            switch ($number % 10) {
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

        return $number . $suffix;
    }

    /**
     * Display a list of all players with optional team filter.
     */
    public function players(): void
    {
        $playerModel = new \App\Models\Player();
        $teamId = $this->get('team_id');
        $page = max(1, (int) $this->get('page', 1));
        $perPage = 24;

        $where = [];
        if ($teamId) {
            $where['team_id'] = (int) $teamId;
        }

        // Get total count
        $totalCount = $playerModel->count($where);

        // Calculate pagination
        $pagination = $this->paginate($totalCount, $page, $perPage);

        // Get paginated players
        $players = $playerModel->paginate($perPage, $pagination['offset'], $where, 'name', 'ASC');

        // Enrich with team data
        $teams = $this->teamModel->all();
        $teamsById = $this->indexById($teams);

        foreach ($players as &$player) {
            $player['team'] = $teamsById[$player['teamId']] ?? null;
        }

        $this->render('public/players', [
            'title' => 'Players',
            'currentPage' => 'players',
            'players' => $players,
            'teams' => $teams,
            'selectedTeamId' => $teamId,
            'pagination' => $pagination,
        ], 'public');
    }

    /**
     * AJAX endpoint to get paginated players list.
     */
    public function getPlayersList(): void
    {
        $playerModel = new \App\Models\Player();
        $teamId = $this->get('team_id');
        $page = max(1, (int) $this->get('page', 1));
        $perPage = 24;

        $where = [];
        if ($teamId) {
            $where['team_id'] = (int) $teamId;
        }

        // Get total count
        $totalCount = $playerModel->count($where);

        // Calculate pagination
        $pagination = $this->paginate($totalCount, $page, $perPage);

        // Get paginated players
        $players = $playerModel->paginate($perPage, $pagination['offset'], $where, 'name', 'ASC');

        // Enrich with team data
        $teams = $this->teamModel->all();
        $teamsById = $this->indexById($teams);

        foreach ($players as &$player) {
            $player['team'] = $teamsById[$player['teamId']] ?? null;
        }

        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

        $this->renderPartial('public/partials/players_grid', [
            'players' => $players,
            'teams' => $teams,
            'basePath' => $basePath,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Display individual player profile with stats and events.
     */
    public function player(string $slug): void
    {
        $playerModel = new \App\Models\Player();
        $player = $playerModel->findWhere('slug', $slug);

        if (!$player) {
            $this->redirect('/players');
            return;
        }

        // Get player stats
        $stats = $playerModel->getStats($player['id']);

        // Get player events
        $matchEventModel = new \App\Models\MatchEvent();
        $events = $matchEventModel->getByPlayer($player['id']);

        // Enrich events with fixture/team data
        $teams = $this->teamModel->all();
        $teamsById = $this->indexById($teams);

        $leagues = $this->leagueModel->all();
        $cups = $this->cupModel->all();

        foreach ($events as &$event) {
            // Find the fixture and competition this event belongs to
            $fixture = null;
            $competition = null;

            // Check leagues
            foreach ($leagues as $league) {
                foreach ($league['fixtures'] ?? [] as $f) {
                    if ($f['id'] === $event['fixtureId'] && $event['fixtureType'] === 'league') {
                        $fixture = $f;
                        $competition = $league;
                        break 2;
                    }
                }
            }

            // Check cups if not found
            if (!$fixture) {
                foreach ($cups as $cup) {
                    foreach ($cup['fixtures'] ?? [] as $f) {
                        if ($f['id'] === $event['fixtureId'] && $event['fixtureType'] === 'cup') {
                            $fixture = $f;
                            $competition = $cup;
                            break 2;
                        }
                    }
                }
            }

            $event['fixture'] = $fixture;
            $event['competition'] = $competition;
            $event['homeTeam'] = $teamsById[$fixture['homeTeamId'] ?? 0] ?? null;
            $event['awayTeam'] = $teamsById[$fixture['awayTeamId'] ?? 0] ?? null;
        }

        // Get player's team
        $player['team'] = $teamsById[$player['teamId']] ?? null;

        $this->render('public/player', [
            'title' => $player['name'],
            'currentPage' => 'players',
            'player' => $player,
            'stats' => $stats,
            'events' => $events,
        ], 'public');
    }

    /**
     * Display top scorers leaderboard with optional league filter.
     */
    public function topScorers(): void
    {
        $playerModel = new \App\Models\Player();
        $leagueId = $this->get('league_id');

        // Get top scorers (limit 20)
        $scorers = $playerModel->getTopScorers(20, $leagueId);

        // Enrich with team data
        $teams = $this->teamModel->all();
        $teamsById = $this->indexById($teams);

        foreach ($scorers as &$scorer) {
            $scorer['team'] = $teamsById[$scorer['teamId']] ?? null;
        }

        $this->render('public/top_scorers', [
            'title' => 'Top Scorers',
            'currentPage' => 'top-scorers',
            'scorers' => $scorers,
            'leagues' => $this->leagueModel->all(),
            'selectedLeagueId' => $leagueId,
        ], 'public');
    }

    /**
     * AJAX endpoint - returns top scorers data as JSON with rendered HTML.
     */
    public function topScorersData(): void
    {
        header('Content-Type: application/json');

        $playerModel = new \App\Models\Player();
        $leagueId = $this->get('league_id');

        // Get top scorers (limit 20)
        $scorers = $playerModel->getTopScorers(20, $leagueId ? (int) $leagueId : null);

        // Enrich with team data
        $teams = $this->teamModel->all();
        $teamsById = $this->indexById($teams);

        foreach ($scorers as &$scorer) {
            $scorer['team'] = $teamsById[$scorer['teamId']] ?? null;
        }

        // Render table HTML using partial
        ob_start();
        include dirname(__DIR__) . '/Views/public/partials/top_scorers_table.php';
        $tableHtml = ob_get_clean();

        echo json_encode([
            'tableHtml' => $tableHtml,
            'count' => count($scorers),
        ]);
    }

    /**
     * Display public fixture detail page.
     */
    public function fixture(string $type, string $competitionSlug, string $fixtureSlug): void
    {
        // Validate fixture type
        if (!in_array($type, ['league', 'cup'])) {
            $this->redirect('/');
            return;
        }

        // Parse fixture slug (format: "home-team-slug-vs-away-team-slug")
        if (!preg_match('/^(.+)-vs-(.+)$/', $fixtureSlug, $matches)) {
            $this->redirect($type === 'league' ? '/leagues' : '/cups');
            return;
        }

        $homeTeamSlug = $matches[1];
        $awayTeamSlug = $matches[2];

        // Load teams to convert slugs to IDs
        $homeTeam = $this->teamModel->findWhere('slug', $homeTeamSlug);
        $awayTeam = $this->teamModel->findWhere('slug', $awayTeamSlug);

        if (!$homeTeam || !$awayTeam) {
            $this->redirect('/');
            return;
        }

        // Load competition and fixtures
        if ($type === 'league') {
            $leagueModel = new \App\Models\League();
            $competition = $leagueModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->redirect('/leagues');
                return;
            }

            // Find fixture by team IDs
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
                $this->redirect('/cups');
                return;
            }

            // Find fixture in rounds
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
            $this->redirect($type === 'league' ? '/leagues' : '/cups');
            return;
        }

        // Get match events timeline
        $matchEventModel = new \App\Models\MatchEvent();
        $events = $matchEventModel->getByFixture($type, $fixtureDetail['id']);

        // Get squad data for both teams
        $playerModel = new \App\Models\Player();
        $homeSquad = $playerModel->getByTeam($homeTeam['id']);
        $awaySquad = $playerModel->getByTeam($awayTeam['id']);

        $this->render('public/fixture', [
            'title' => $fixtureDetail['homeTeamName'] . ' vs ' . $fixtureDetail['awayTeamName'],
            'currentPage' => $type . 's',
            'fixtureType' => $type,
            'competition' => $competition,
            'fixture' => $fixtureDetail,
            'events' => $events,
            'homeSquad' => $homeSquad,
            'awaySquad' => $awaySquad,
        ], 'public');
    }

    /**
     * Find fixture by team IDs in a fixture list.
     * Prefers matches with results or video content if multiple exist.
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
}
