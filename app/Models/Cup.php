<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Cup model for managing knockout cup competitions.
 * Handles bracket generation and winner advancement.
 */
class Cup extends Model
{
    protected function getDataPath(): string
    {
        return dirname(__DIR__, 2) . '/data/cups/cups.json';
    }

    /**
     * Generate single-elimination bracket for a cup.
     * Teams are seeded randomly.
     */
    public function generateBracket(array $teamIds, string $startDate = '', string $frequency = 'weekly', string $matchTime = '15:00'): array
    {
        $rounds = [];
        $numTeams = count($teamIds);

        if ($numTeams < 2) {
            return $rounds;
        }

        // Shuffle for random seeding
        shuffle($teamIds);

        // Calculate number of rounds needed
        $numRounds = (int) ceil(log($numTeams, 2));

        // Round names
        $roundNames = $this->getRoundNames($numRounds);

        // Date calculation logic
        $currentDate = $startDate ? new \DateTime($startDate) : null;
        $roundGap = $this->getRoundGap($frequency);

        // First round - may have byes if not power of 2
        $targetSize = pow(2, $numRounds);
        $byesNeeded = $targetSize - $numTeams;

        $firstRoundFixtures = [];
        $teamsInRound = $teamIds;

        // Teams with byes go straight through
        $advancingFromByes = array_splice($teamsInRound, 0, $byesNeeded);

        // Pair remaining teams for first round
        while (count($teamsInRound) >= 2) {
            $home = array_shift($teamsInRound);
            $away = array_shift($teamsInRound);

            $firstRoundFixtures[] = [
                'id' => $this->generateId(),
                'homeTeamId' => $home,
                'awayTeamId' => $away,
                'date' => $currentDate ? $currentDate->format('Y-m-d') : '',
                'time' => $matchTime,
                'result' => null,
            ];
        }

        if (!empty($firstRoundFixtures)) {
            $rounds[] = [
                'name' => $roundNames[0],
                'fixtures' => $firstRoundFixtures,
            ];
        }

        // Generate placeholder rounds
        $teamsInNextRound = count($advancingFromByes) + count($firstRoundFixtures);

        for ($r = 1; $r < $numRounds; $r++) {
            if ($currentDate) {
                $currentDate->modify($roundGap);
            }
            $roundFixtures = [];
            $matchesInRound = (int) ($teamsInNextRound / 2);

            for ($i = 0; $i < $matchesInRound; $i++) {
                $roundFixtures[] = [
                    'id' => $this->generateId(),
                    'homeTeamId' => null,
                    'awayTeamId' => null,
                    'date' => $currentDate ? $currentDate->format('Y-m-d') : '',
                    'time' => $matchTime,
                    'result' => null,
                ];
            }

            $rounds[] = [
                'name' => $roundNames[$r],
                'fixtures' => $roundFixtures,
            ];

            $teamsInNextRound = $matchesInRound;
        }

        return $rounds;
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
     * Reschedule only unplayed fixtures in a cup while keeping the structure.
     */
    public function rescheduleUnplayed(string $cupId, string $startDate, string $frequency, string $matchTime): bool
    {
        $cup = null;
        $cupIndex = -1;
        foreach ($this->data as $index => $c) {
            if ($c['id'] === $cupId) {
                $cup = $c;
                $cupIndex = $index;
                break;
            }
        }

        if (!$cup || $cupIndex === -1) {
            return false;
        }

        $currentDate = new \DateTime($startDate);
        $roundGap = $this->getRoundGap($frequency);

        foreach ($cup['rounds'] as $r => $round) {
            foreach ($round['fixtures'] as $f => $fixture) {
                // Only update if no result
                if ($fixture['result'] === null) {
                    $this->data[$cupIndex]['rounds'][$r]['fixtures'][$f]['date'] = $currentDate->format('Y-m-d');
                    $this->data[$cupIndex]['rounds'][$r]['fixtures'][$f]['time'] = $matchTime;
                }
            }
            // All matches in a round happen roughly at the same time/week
            $currentDate->modify($roundGap);
        }

        return $this->save();
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
    public function updateFixtureResult(string $cupId, string $fixtureId, array $result): bool
    {
        foreach ($this->data as $cupIndex => $cup) {
            if ($cup['id'] !== $cupId) {
                continue;
            }

            foreach ($cup['rounds'] as $roundIndex => $round) {
                foreach ($round['fixtures'] as $fixtureIndex => $fixture) {
                    if ($fixture['id'] !== $fixtureId) {
                        continue;
                    }

                    // Store the result
                    $this->data[$cupIndex]['rounds'][$roundIndex]['fixtures'][$fixtureIndex]['result'] = $result;
                    $this->data[$cupIndex]['updated_at'] = date('Y-m-d H:i:s');

                    // Determine winner
                    $winnerId = $result['winnerId'] ?? null;
                    if (!$winnerId) {
                        $winnerId = $this->determineWinner($result);
                    }

                    if ($winnerId) {
                        // Advance winner to next round
                        $this->advanceWinner($cupIndex, $roundIndex, $fixtureIndex, $winnerId);
                    }

                    return $this->save();
                }
            }
        }
        return false;
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
     * Advance winner to next round fixture.
     */
    private function advanceWinner(int $cupIndex, int $roundIndex, int $fixtureIndex, string $winner): void
    {
        $cup = $this->data[$cupIndex];
        $nextRoundIndex = $roundIndex + 1;

        if (!isset($cup['rounds'][$nextRoundIndex])) {
            return;
        }

        $fixture = $cup['rounds'][$roundIndex]['fixtures'][$fixtureIndex];
        $winnerId = $winner === 'home' ? $fixture['homeTeamId'] : $fixture['awayTeamId'];

        // Calculate which fixture in next round
        $nextFixtureIndex = (int) floor($fixtureIndex / 2);
        $isHome = ($fixtureIndex % 2) === 0;

        if ($isHome) {
            $this->data[$cupIndex]['rounds'][$nextRoundIndex]['fixtures'][$nextFixtureIndex]['homeTeamId'] = $winnerId;
        } else {
            $this->data[$cupIndex]['rounds'][$nextRoundIndex]['fixtures'][$nextFixtureIndex]['awayTeamId'] = $winnerId;
        }
    }

    /**
     * Update fixture date and time.
     */
    public function updateFixtureDateTime(string $cupId, string $fixtureId, string $date, string $time): bool
    {
        foreach ($this->data as $cupIndex => $cup) {
            if ($cup['id'] !== $cupId) {
                continue;
            }

            foreach ($cup['rounds'] as $roundIndex => $round) {
                foreach ($round['fixtures'] as $fixtureIndex => $fixture) {
                    if ($fixture['id'] === $fixtureId) {
                        $this->data[$cupIndex]['rounds'][$roundIndex]['fixtures'][$fixtureIndex]['date'] = $date;
                        $this->data[$cupIndex]['rounds'][$roundIndex]['fixtures'][$fixtureIndex]['time'] = $time;
                        $this->data[$cupIndex]['updated_at'] = date('Y-m-d H:i:s');
                        return $this->save();
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get cups by season ID.
     */
    public function getBySeasonId(string $seasonId): array
    {
        return array_values(array_filter($this->data, function ($cup) use ($seasonId) {
            return ($cup['seasonId'] ?? '') === $seasonId;
        }));
    }

    /**
     * Get fixture by ID within a cup.
     */
    public function getFixture(string $cupId, string $fixtureId): ?array
    {
        $cup = $this->find($cupId);
        if (!$cup) {
            return null;
        }

        foreach ($cup['rounds'] ?? [] as $round) {
            foreach ($round['fixtures'] ?? [] as $fixture) {
                if ($fixture['id'] === $fixtureId) {
                    $fixture['roundName'] = $round['name'];
                    return $fixture;
                }
            }
        }
        return null;
    }
    /**
     * Override create to add slug.
     */
    public function create(array $record): array
    {
        if (isset($record['name']) && !isset($record['slug'])) {
            $record['slug'] = self::slugify($record['name']);
        }
        return parent::create($record);
    }

    /**
     * Override update to update slug if name changes.
     */
    public function update(string $id, array $updates): bool
    {
        if (isset($updates['name'])) {
            $updates['slug'] = self::slugify($updates['name']);
        }
        return parent::update($id, $updates);
    }
}
