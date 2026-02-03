<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;
use App\Models\Traits\HasTeams;

/**
 * Cup model for managing knockout cup competitions.
 * Handles bracket generation and winner advancement.
 */
class Cup extends Model
{
    use HasTeams;

    protected function getTableName(): string
    {
        return 'cups';
    }

    /**
     * Implement HasTeams trait requirement.
     */
    protected function getTeamJoinTable(): string
    {
        return 'cup_teams';
    }

    /**
     * Implement HasTeams trait requirement.
     */
    protected function getCompetitionIdColumn(): string
    {
        return 'cup_id';
    }

    /**
     * Override create to auto-generate slug and handle teams.
     */
    public function create(array $record): array
    {
        // Extract teams if provided
        $teamIds = $record['team_ids'] ?? [];
        unset($record['team_ids']);

        // Auto-generate unique slug if not provided
        if (isset($record['name']) && !isset($record['slug'])) {
            $record['slug'] = $this->generateUniqueSlug($record['name']);
        }

        // Create the cup
        $cup = parent::create($record);

        // Add teams if provided
        if (!empty($teamIds)) {
            $this->setTeams($cup['id'], $teamIds);

            // Auto-generate bracket fixtures
            $this->generateBracket(
                $cup['id'],
                $teamIds,
                $record['start_date'] ?? '',
                $record['frequency'] ?? 'weekly',
                $record['match_time'] ?? '15:00'
            );
        }

        return $cup;
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
     * Override all to include teams and rounds.
     */
    public function all(): array
    {
        $cups = parent::all();

        foreach ($cups as &$cup) {
            $cup['teamIds'] = $this->getTeamIds($cup['id']);
            $cup['rounds'] = $this->getRounds($cup['id']);
        }

        return $cups;
    }

    /**
     * Override find to include teams and rounds with fixtures.
     */
    public function find(int|string $id): ?array
    {
        $cup = parent::find($id);

        if ($cup) {
            $cup['teamIds'] = $this->getTeamIds($id);
            $cup['rounds'] = $this->getRounds($id);
        }

        return $cup;
    }


    /**
     * Get rounds with fixtures for a cup.
     */
    public function getRounds(int|string $cupId): array
    {
        // Get rounds
        $stmt = $this->db->prepare("
            SELECT id, name, round_order
            FROM cup_rounds
            WHERE cup_id = ?
            ORDER BY round_order
        ");
        $stmt->execute([$cupId]);
        $rounds = $stmt->fetchAll();

        // Get fixtures for each round
        foreach ($rounds as &$round) {
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
                    away_cards,
                    extra_time,
                    home_score_et,
                    away_score_et,
                    penalties,
                    home_pens,
                    away_pens,
                    winner
                FROM cup_fixtures
                WHERE round_id = ?
                ORDER BY id
            ");
            $stmt->execute([$round['id']]);
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
                        'extraTime' => (bool) $fixture['extra_time'],
                        'homeScoreET' => $fixture['home_score_et'],
                        'awayScoreET' => $fixture['away_score_et'],
                        'penalties' => (bool) $fixture['penalties'],
                        'homePens' => $fixture['home_pens'],
                        'awayPens' => $fixture['away_pens'],
                        'winnerId' => $this->getWinnerTeamId($fixture),
                    ];
                } else {
                    $fixture['result'] = null;
                }

                // Remove individual fields and original column names
                unset(
                    $fixture['home_score'],
                    $fixture['away_score'],
                    $fixture['home_scorers'],
                    $fixture['away_scorers'],
                    $fixture['home_cards'],
                    $fixture['away_cards'],
                    $fixture['extra_time'],
                    $fixture['home_score_et'],
                    $fixture['away_score_et'],
                    $fixture['penalties'],
                    $fixture['home_pens'],
                    $fixture['away_pens'],
                    $fixture['winner'],
                    $fixture['home_team_id'],
                    $fixture['away_team_id'],
                    $fixture['match_date'],
                    $fixture['match_time']
                );
            }

            $round['fixtures'] = $fixtures;
            unset($round['round_order']);
        }

        return $rounds;
    }

    /**
     * Get winner team ID from fixture row.
     */
    private function getWinnerTeamId(array $fixture): ?int
    {
        if ($fixture['winner'] === 'home') {
            return $fixture['homeTeamId'];
        } elseif ($fixture['winner'] === 'away') {
            return $fixture['awayTeamId'];
        }
        return null;
    }

    /**
     * Generate single-elimination bracket for a cup.
     * Creates rounds and fixtures in the database.
     */
    public function generateBracket(int|string $cupId, array $teamIds, string $startDate = '', string $frequency = 'weekly', string $matchTime = '15:00'): bool
    {
        $numTeams = count($teamIds);

        if ($numTeams < 2) {
            return false;
        }

        // Delete existing rounds and fixtures (CASCADE will handle fixtures)
        $stmt = $this->db->prepare("DELETE FROM cup_rounds WHERE cup_id = ?");
        $stmt->execute([$cupId]);

        // Shuffle for random seeding
        shuffle($teamIds);

        // Calculate number of rounds needed
        $numRounds = (int) ceil(log($numTeams, 2));
        $roundNames = $this->getRoundNames($numRounds);

        // Date calculation logic
        $currentDate = $startDate ? new \DateTime($startDate) : new \DateTime();
        $roundGap = $this->getRoundGap($frequency);

        // First round - may have byes if not power of 2
        $targetSize = pow(2, $numRounds);
        $byesNeeded = $targetSize - $numTeams;

        $teamsInRound = $teamIds;

        // Teams with byes go straight through
        $advancingFromByes = array_splice($teamsInRound, 0, $byesNeeded);

        // Create rounds
        $roundIds = [];
        for ($r = 0; $r < $numRounds; $r++) {
            $stmt = $this->db->prepare("
                INSERT INTO cup_rounds (cup_id, name, round_order)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$cupId, $roundNames[$r], $r]);
            $roundIds[$r] = $this->db->lastInsertId();
        }

        // Create first round fixtures
        $fixtureStmt = $this->db->prepare("
            INSERT INTO cup_fixtures
            (cup_id, round_id, home_team_id, away_team_id, match_date, match_time, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        while (count($teamsInRound) >= 2) {
            $home = array_shift($teamsInRound);
            $away = array_shift($teamsInRound);

            $fixtureStmt->execute([
                $cupId,
                $roundIds[0],
                $home,
                $away,
                $currentDate->format('Y-m-d'),
                $matchTime
            ]);
        }

        // Generate placeholder rounds for subsequent rounds
        $teamsInNextRound = count($advancingFromByes) + ((count($teamIds) - $byesNeeded) / 2);

        for ($r = 1; $r < $numRounds; $r++) {
            $currentDate->modify($roundGap);
            $matchesInRound = (int) ($teamsInNextRound / 2);

            for ($i = 0; $i < $matchesInRound; $i++) {
                $fixtureStmt->execute([
                    $cupId,
                    $roundIds[$r],
                    null,  // TBD
                    null,  // TBD
                    $currentDate->format('Y-m-d'),
                    $matchTime
                ]);
            }

            $teamsInNextRound = $matchesInRound;
        }

        return true;
    }

    /**
     * Map frequency string to date modification string.
     */
    private function getRoundGap(string $frequency): string
    {
        return match ($frequency) {
            'fortnightly' => '+14 days',
            'monthly' => '+1 month',
            default => '+7 days',
        };
    }

    /**
     * Get round names based on number of rounds.
     */
    private function getRoundNames(int $numRounds): array
    {
        $names = [];
        for ($i = $numRounds; $i >= 1; $i--) {
            switch ($i) {
                case 1:
                    $names[] = 'Final';
                    break;
                case 2:
                    $names[] = 'Semi-Final';
                    break;
                case 3:
                    $names[] = 'Quarter-Final';
                    break;
                default:
                    $names[] = 'Round of ' . pow(2, $i);
            }
        }
        return $names;
    }

    /**
     * Update a fixture result and advance winner.
     */
    public function updateFixtureResult(int|string $cupId, int|string $fixtureId, array $result): bool
    {
        // Determine winner
        $winnerId = $result['winnerId'] ?? null;
        if (!$winnerId) {
            $winner = $this->determineWinner($result);
        } else {
            // Get fixture to determine home/away
            $fixture = $this->getFixtureById($fixtureId);
            if ($fixture) {
                $winner = $winnerId == $fixture['homeTeamId'] ? 'home' : 'away';
            } else {
                $winner = null;
            }
        }

        // Update fixture
        $stmt = $this->db->prepare("
            UPDATE cup_fixtures
            SET home_score = ?,
                away_score = ?,
                home_scorers = ?,
                away_scorers = ?,
                home_cards = ?,
                away_cards = ?,
                extra_time = ?,
                home_score_et = ?,
                away_score_et = ?,
                penalties = ?,
                home_pens = ?,
                away_pens = ?,
                winner = ?
            WHERE id = ? AND cup_id = ?
        ");

        $success = $stmt->execute([
            $result['homeScore'] ?? null,
            $result['awayScore'] ?? null,
            $result['homeScorers'] ?? '',
            $result['awayScorers'] ?? '',
            $result['homeCards'] ?? '',
            $result['awayCards'] ?? '',
            ($result['extraTime'] ?? false) ? 1 : 0,
            $result['homeScoreET'] ?? null,
            $result['awayScoreET'] ?? null,
            ($result['penalties'] ?? false) ? 1 : 0,
            $result['homePens'] ?? null,
            $result['awayPens'] ?? null,
            $winner,
            $fixtureId,
            $cupId
        ]);

        // Advance winner to next round
        if ($success && $winner) {
            $this->advanceWinner($fixtureId, $winner);
        }

        return $success;
    }

    /**
     * Determine the winner from a result.
     */
    private function determineWinner(array $result): ?string
    {
        $homeScore = (int) ($result['homeScore'] ?? 0);
        $awayScore = (int) ($result['awayScore'] ?? 0);

        // Check for extra time
        if (($result['extraTime'] ?? false) && isset($result['homeScoreET'], $result['awayScoreET'])) {
            $homeScore = (int) $result['homeScoreET'];
            $awayScore = (int) $result['awayScoreET'];
        }

        // Check for penalties
        if (($result['penalties'] ?? false) && isset($result['homePens'], $result['awayPens'])) {
            $homePens = (int) $result['homePens'];
            $awayPens = (int) $result['awayPens'];
            return $homePens > $awayPens ? 'home' : 'away';
        }

        if ($homeScore > $awayScore) {
            return 'home';
        } elseif ($awayScore > $homeScore) {
            return 'away';
        }

        return null;
    }

    /**
     * Get fixture by ID (simple query without full structure).
     */
    private function getFixtureById(int|string $fixtureId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, round_id, home_team_id as homeTeamId, away_team_id as awayTeamId
            FROM cup_fixtures
            WHERE id = ?
        ");
        $stmt->execute([$fixtureId]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Advance winner to next round fixture.
     */
    private function advanceWinner(int|string $fixtureId, string $winner): void
    {
        // Get current fixture details
        $fixture = $this->getFixtureById($fixtureId);
        if (!$fixture) {
            return;
        }

        $winnerId = $winner === 'home' ? $fixture['homeTeamId'] : $fixture['awayTeamId'];
        $roundId = $fixture['round_id'];

        // Get cup_id and round_order
        $stmt = $this->db->prepare("
            SELECT cup_id, round_order
            FROM cup_rounds
            WHERE id = ?
        ");
        $stmt->execute([$roundId]);
        $round = $stmt->fetch();

        if (!$round) {
            return;
        }

        // Find next round
        $stmt = $this->db->prepare("
            SELECT id
            FROM cup_rounds
            WHERE cup_id = ? AND round_order = ?
        ");
        $stmt->execute([$round['cup_id'], $round['round_order'] + 1]);
        $nextRound = $stmt->fetch();

        if (!$nextRound) {
            return;  // Already in final
        }

        // Get fixtures in current round ordered by ID
        $stmt = $this->db->prepare("
            SELECT id
            FROM cup_fixtures
            WHERE round_id = ?
            ORDER BY id
        ");
        $stmt->execute([$roundId]);
        $fixturesInRound = $stmt->fetchAll();

        // Find position of current fixture
        $fixturePosition = null;
        foreach ($fixturesInRound as $index => $f) {
            if ($f['id'] == $fixtureId) {
                $fixturePosition = $index;
                break;
            }
        }

        if ($fixturePosition === null) {
            return;
        }

        // Calculate which fixture in next round and whether home/away
        $nextFixtureIndex = (int) floor($fixturePosition / 2);
        $isHome = ($fixturePosition % 2) === 0;

        // Get next round fixtures ordered by ID
        $stmt = $this->db->prepare("
            SELECT id
            FROM cup_fixtures
            WHERE round_id = ?
            ORDER BY id
        ");
        $stmt->execute([$nextRound['id']]);
        $nextRoundFixtures = $stmt->fetchAll();

        if (!isset($nextRoundFixtures[$nextFixtureIndex])) {
            return;
        }

        $nextFixtureId = $nextRoundFixtures[$nextFixtureIndex]['id'];

        // Update next round fixture
        $field = $isHome ? 'home_team_id' : 'away_team_id';
        $stmt = $this->db->prepare("UPDATE cup_fixtures SET {$field} = ? WHERE id = ?");
        $stmt->execute([$winnerId, $nextFixtureId]);
    }

    /**
     * Update fixture date and time.
     */
    public function updateFixtureDateTime(int|string $cupId, int|string $fixtureId, string $date, string $time): bool
    {
        $stmt = $this->db->prepare("
            UPDATE cup_fixtures
            SET match_date = ?, match_time = ?
            WHERE cup_id = ? AND id = ?
        ");

        return $stmt->execute([$date, $time, $cupId, $fixtureId]);
    }

    /**
     * Reschedule only unplayed fixtures in a cup while keeping the structure.
     */
    public function rescheduleUnplayed(int|string $cupId, string $startDate, string $frequency, string $matchTime): bool
    {
        $rounds = $this->getRounds($cupId);
        $currentDate = new \DateTime($startDate);
        $roundGap = $this->getRoundGap($frequency);

        $stmt = $this->db->prepare("
            UPDATE cup_fixtures
            SET match_date = ?, match_time = ?
            WHERE id = ?
        ");

        foreach ($rounds as $round) {
            foreach ($round['fixtures'] as $fixture) {
                // Only update if no result
                if ($fixture['result'] === null) {
                    $stmt->execute([
                        $currentDate->format('Y-m-d'),
                        $matchTime,
                        $fixture['id']
                    ]);
                }
            }
            $currentDate->modify($roundGap);
        }

        return true;
    }

    /**
     * Get cups by season ID.
     */
    public function getBySeasonId(int|string $seasonId): array
    {
        $cups = $this->where('season_id', $seasonId);

        // Load rounds and teams for each cup
        foreach ($cups as &$cup) {
            $cup['teamIds'] = $this->getTeamIds($cup['id']);
            $cup['rounds'] = $this->getRounds($cup['id']);
        }

        return $cups;
    }

    /**
     * Get fixture by ID within a cup.
     */
    public function getFixture(int|string $cupId, int|string $fixtureId): ?array
    {
        $cup = $this->find($cupId);
        if (!$cup) {
            return null;
        }

        foreach ($cup['rounds'] ?? [] as $round) {
            foreach ($round['fixtures'] ?? [] as $fixture) {
                if ($fixture['id'] == $fixtureId) {
                    $fixture['roundName'] = $round['name'];
                    return $fixture;
                }
            }
        }

        return null;
    }

    /**
     * Override findWhere to include teams and rounds with fixtures.
     */
    public function findWhere(string $field, mixed $value): ?array
    {
        $cup = parent::findWhere($field, $value);

        if ($cup) {
            $cup['teamIds'] = $this->getTeamIds($cup['id']);
            $cup['rounds'] = $this->getRounds($cup['id']);
        }

        return $cup;
    }
}
