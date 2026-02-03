<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;
use App\Models\Traits\HasTeams;

/**
 * League model for managing football leagues.
 * Handles fixtures generation and standings calculation.
 */
class League extends Model
{
    use HasTeams;

    protected function getTableName(): string
    {
        return 'leagues';
    }

    /**
     * Implement HasTeams trait requirement.
     */
    protected function getTeamJoinTable(): string
    {
        return 'league_teams';
    }

    /**
     * Implement HasTeams trait requirement.
     */
    protected function getCompetitionIdColumn(): string
    {
        return 'league_id';
    }

    /**
     * Override create to auto-generate slug and handle teams.
     */
    public function create(array $record): array
    {
        // Extract teams if provided
        $teamIds = $record['teamIds'] ?? [];
        unset($record['teamIds']);

        // Auto-generate unique slug if not provided
        if (isset($record['name']) && !isset($record['slug'])) {
            $record['slug'] = $this->generateUniqueSlug($record['name']);
        }

        // Create the league
        $league = parent::create($record);

        // Add teams if provided
        if (!empty($teamIds)) {
            $this->setTeams($league['id'], $teamIds);
        }

        return $league;
    }

    /**
     * Override update to update slug if name changes.
     */
    public function update(int|string $id, array $updates): bool
    {
        if (isset($updates['name'])) {
            $updates['slug'] = $this->generateUniqueSlug($updates['name'], $id);
        }

        return parent::update($id, $updates);
    }

    /**
     * Override all to include teams and fixtures.
     */
    public function all(): array
    {
        $leagues = parent::all();

        foreach ($leagues as &$league) {
            $league['teamIds'] = $this->getTeamIds($league['id']);
            $league['fixtures'] = $this->getFixtures($league['id']);
        }

        return $leagues;
    }

    /**
     * Override find to include teams and fixtures.
     */
    public function find(int|string $id): ?array
    {
        $league = parent::find($id);

        if ($league) {
            $league['teamIds'] = $this->getTeamIds($id);
            $league['fixtures'] = $this->getFixtures($id);
        }

        return $league;
    }

    /**
     * Add a single team to a league.
     */
    public function addTeam(int|string $leagueId, int|string $teamId): void
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO league_teams (league_id, team_id) VALUES (?, ?)");
        $stmt->execute([$leagueId, $teamId]);
    }

    /**
     * Get fixtures for a league.
     */
    public function getFixtures(int|string $leagueId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                id,
                home_team_id as homeTeamId,
                away_team_id as awayTeamId,
                match_date as date,
                match_time as time,
                home_score,
                away_score,
                home_scorers,
                away_scorers,
                home_cards,
                away_cards
            FROM league_fixtures
            WHERE league_id = ?
            ORDER BY match_date, match_time
        ");
        $stmt->execute([$leagueId]);

        $fixtures = $stmt->fetchAll();

        // Convert result fields to 'result' object if scores exist
        foreach ($fixtures as &$fixture) {
            if ($fixture['home_score'] !== null) {
                $fixture['result'] = [
                    'homeScore' => $fixture['home_score'],
                    'awayScore' => $fixture['away_score'],
                    'homeScorers' => $fixture['home_scorers'] ? json_decode($fixture['home_scorers'], true) : [],
                    'awayScorers' => $fixture['away_scorers'] ? json_decode($fixture['away_scorers'], true) : [],
                    'homeCards' => $fixture['home_cards'] ? json_decode($fixture['home_cards'], true) : [],
                    'awayCards' => $fixture['away_cards'] ? json_decode($fixture['away_cards'], true) : [],
                ];
            } else {
                $fixture['result'] = null;
            }

            // Remove individual score fields and original column names
            unset($fixture['home_score'], $fixture['away_score']);
            unset($fixture['home_scorers'], $fixture['away_scorers']);
            unset($fixture['home_cards'], $fixture['away_cards']);
            unset($fixture['home_team_id'], $fixture['away_team_id']);
            unset($fixture['match_date'], $fixture['match_time']);
        }

        return $fixtures;
    }

    /**
     * Get the count of fixtures for a league.
     */
    public function getFixturesCount(int|string $leagueId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM league_fixtures WHERE league_id = ?");
        $stmt->execute([$leagueId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Delete only unplayed fixtures (fixtures without scores).
     */
    public function deleteUnplayedFixtures(int|string $leagueId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM league_fixtures
            WHERE league_id = ?
            AND home_score IS NULL
            AND away_score IS NULL
        ");
        return $stmt->execute([$leagueId]);
    }

    /**
     * Generate round-robin fixtures for a league.
     * Creates fixtures in the database.
     *
     * @param bool $deleteExisting Whether to delete all existing fixtures before generating
     */
    public function generateFixtures(int|string $leagueId, array $teamIds, string $startDate, string $frequency = 'weekly', string $matchTime = '15:00', bool $deleteExisting = true): bool
    {
        $numTeams = count($teamIds);

        if ($numTeams < 2) {
            return false;
        }

        // Delete existing fixtures if requested
        if ($deleteExisting) {
            $stmt = $this->db->prepare("DELETE FROM league_fixtures WHERE league_id = ?");
            $stmt->execute([$leagueId]);
        }

        $teams = $teamIds;
        // If odd number of teams, add null for a BYE
        $hasBye = false;
        if ($numTeams % 2 !== 0) {
            $teams[] = null;
            $numTeams++;
            $hasBye = true;
        }

        $roundsPerHalf = $numTeams - 1;
        $matchesPerRound = $numTeams / 2;
        $currentDate = new \DateTime($startDate);

        // Determine the gap between rounds based on frequency
        $roundGap = match ($frequency) {
            'fortnightly' => '+14 days',
            'monthly' => '+1 month',
            default => '+7 days',
        };

        $stmt = $this->db->prepare("
            INSERT INTO league_fixtures
            (league_id, home_team_id, away_team_id, match_date, match_time, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        // First half: each team plays each other once
        for ($round = 0; $round < $roundsPerHalf; $round++) {
            for ($i = 0; $i < $matchesPerRound; $i++) {
                $home = $teams[$i];
                $away = $teams[$numTeams - 1 - $i];

                if ($home !== null && $away !== null) {
                    // Alternate home/away for the fixed team at index 0
                    if ($i === 0 && $round % 2 === 1) {
                        [$home, $away] = [$away, $home];
                    }

                    $stmt->execute([
                        $leagueId,
                        $home,
                        $away,
                        $currentDate->format('Y-m-d'),
                        $matchTime
                    ]);
                }
            }
            $currentDate->modify($roundGap);

            // Rotate teams (keep the first team fixed)
            $lastTeam = array_pop($teams);
            array_splice($teams, 1, 0, [$lastTeam]);
        }

        // Add a gap between halves
        $currentDate->modify($roundGap);

        // Second half: reverse fixtures
        $teams = $teamIds;
        if ($hasBye) {
            $teams[] = null;
        }

        for ($round = 0; $round < $roundsPerHalf; $round++) {
            for ($i = 0; $i < $matchesPerRound; $i++) {
                // Reverse home/away relative to first half logic
                $home = $teams[$numTeams - 1 - $i];
                $away = $teams[$i];

                if ($home !== null && $away !== null) {
                    // Alternate home/away for the fixed team at index 0 (opposite of first half)
                    if ($i === 0 && $round % 2 === 1) {
                        [$home, $away] = [$away, $home];
                    }

                    $stmt->execute([
                        $leagueId,
                        $home,
                        $away,
                        $currentDate->format('Y-m-d'),
                        $matchTime
                    ]);
                }
            }
            $currentDate->modify($roundGap);

            // Rotate teams (keep the first team fixed)
            $lastTeam = array_pop($teams);
            array_splice($teams, 1, 0, [$lastTeam]);
        }

        return true;
    }

    /**
     * Calculate league standings from fixture results.
     */
    public function calculateStandings(int|string $leagueId, array $teams): array
    {
        $teamIds = $this->getTeamIds($leagueId);
        $fixtures = $this->getFixtures($leagueId);

        $standings = [];

        // Initialize standings for each team
        foreach ($teamIds as $teamId) {
            $team = $this->findTeamById($teams, $teamId);
            $standings[$teamId] = [
                'teamId' => $teamId,
                'teamSlug' => $team['slug'] ?? (string)$teamId,
                'teamName' => $team['name'] ?? 'Unknown',
                'teamColour' => $team['colour'] ?? '#000000',
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'goalsFor' => 0,
                'goalsAgainst' => 0,
                'goalDifference' => 0,
                'points' => 0,
                'form' => [],
            ];
        }

        // Process fixtures
        foreach ($fixtures as $fixture) {
            if (!isset($fixture['result']) || $fixture['result'] === null) {
                continue;
            }

            $homeId = $fixture['homeTeamId'];
            $awayId = $fixture['awayTeamId'];
            $homeScore = (int) ($fixture['result']['homeScore'] ?? 0);
            $awayScore = (int) ($fixture['result']['awayScore'] ?? 0);

            if (!isset($standings[$homeId]) || !isset($standings[$awayId])) {
                continue;
            }

            // Update played
            $standings[$homeId]['played']++;
            $standings[$awayId]['played']++;

            // Update goals
            $standings[$homeId]['goalsFor'] += $homeScore;
            $standings[$homeId]['goalsAgainst'] += $awayScore;
            $standings[$awayId]['goalsFor'] += $awayScore;
            $standings[$awayId]['goalsAgainst'] += $homeScore;

            // Update wins/draws/losses, points, and form
            if ($homeScore > $awayScore) {
                $standings[$homeId]['won']++;
                $standings[$homeId]['points'] += 3;
                $standings[$homeId]['form'][] = 'W';
                $standings[$awayId]['lost']++;
                $standings[$awayId]['form'][] = 'L';
            } elseif ($awayScore > $homeScore) {
                $standings[$awayId]['won']++;
                $standings[$awayId]['points'] += 3;
                $standings[$awayId]['form'][] = 'W';
                $standings[$homeId]['lost']++;
                $standings[$homeId]['form'][] = 'L';
            } else {
                $standings[$homeId]['drawn']++;
                $standings[$awayId]['drawn']++;
                $standings[$homeId]['points']++;
                $standings[$awayId]['points']++;
                $standings[$homeId]['form'][] = 'D';
                $standings[$awayId]['form'][] = 'D';
            }
        }

        // Calculate goal difference and trim form
        foreach ($standings as &$row) {
            $row['goalDifference'] = $row['goalsFor'] - $row['goalsAgainst'];
            $row['form'] = array_slice($row['form'], -5);
        }

        // Sort by points, then goal difference, then goals scored
        uasort($standings, function ($a, $b) {
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
     * Find team by ID in teams array.
     */
    private function findTeamById(array $teams, int|string $id): ?array
    {
        foreach ($teams as $team) {
            if ($team['id'] == $id) {
                return $team;
            }
        }
        return null;
    }

    /**
     * Get fixture by ID.
     */
    public function getFixture(int|string $leagueId, int|string $fixtureId): ?array
    {
        $fixtures = $this->getFixtures($leagueId);

        foreach ($fixtures as $fixture) {
            if ($fixture['id'] == $fixtureId) {
                return $fixture;
            }
        }

        return null;
    }

    /**
     * Update a fixture result.
     */
    public function updateFixtureResult(int|string $leagueId, int|string $fixtureId, array $result): bool
    {
        $stmt = $this->db->prepare("
            UPDATE league_fixtures
            SET home_score = ?,
                away_score = ?,
                home_scorers = ?,
                away_scorers = ?,
                home_cards = ?,
                away_cards = ?
            WHERE league_id = ? AND id = ?
        ");

        return $stmt->execute([
            $result['homeScore'] ?? null,
            $result['awayScore'] ?? null,
            $result['homeScorers'] ?? '',
            $result['awayScorers'] ?? '',
            $result['homeCards'] ?? '',
            $result['awayCards'] ?? '',
            $leagueId,
            $fixtureId
        ]);
    }

    /**
     * Update fixture date and time.
     */
    public function updateFixtureDateTime(int|string $leagueId, int|string $fixtureId, string $date, string $time): bool
    {
        $stmt = $this->db->prepare("
            UPDATE league_fixtures
            SET match_date = ?, match_time = ?
            WHERE league_id = ? AND id = ?
        ");

        return $stmt->execute([$date, $time, $leagueId, $fixtureId]);
    }

    /**
     * Get all leagues for a season.
     */
    public function getBySeasonId(int|string $seasonId): array
    {
        $leagues = $this->where('season_id', $seasonId);

        // Load fixtures and teams for each league
        foreach ($leagues as &$league) {
            $league['teamIds'] = $this->getTeamIds($league['id']);
            $league['fixtures'] = $this->getFixtures($league['id']);
        }

        return $leagues;
    }

    /**
     * Override findWhere to include teams and fixtures.
     */
    public function findWhere(string $field, mixed $value): ?array
    {
        $league = parent::findWhere($field, $value);

        if ($league) {
            $league['teamIds'] = $this->getTeamIds($league['id']);
            $league['fixtures'] = $this->getFixtures($league['id']);
        }

        return $league;
    }
}
