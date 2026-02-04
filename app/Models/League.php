<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * League model for managing football leagues.
 * Handles fixtures generation and standings calculation.
 */
class League extends Model
{
    protected function getDataPath(): string
    {
        return dirname(__DIR__, 2) . '/data/leagues/leagues.json';
    }

    /**
     * Generate round-robin fixtures for a league.
     * Each team plays every other team home and away.
     */
    public function generateFixtures(array $teamIds, string $startDate, string $frequency = 'weekly', string $matchTime = '15:00'): array
    {
        $fixtures = [];
        $numTeams = count($teamIds);

        if ($numTeams < 2) {
            return $fixtures;
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

        // First half: each team plays each other once
        for ($round = 0; $round < $roundsPerHalf; $round++) {
            for ($i = 0; $i < $matchesPerRound; $i++) {
                $home = $teams[$i];
                $away = $teams[$numTeams - 1 - $i];

                if ($home !== null && $away !== null) {
                    // Alternate home/away for the fixed team at index 0
                    if ($i === 0 && $round % 2 === 1) {
                        $temp = $home;
                        $home = $away;
                        $away = $temp;
                    }

                    $fixtures[] = [
                        'id' => $this->generateId(),
                        'homeTeamId' => $home,
                        'awayTeamId' => $away,
                        'date' => $currentDate->format('Y-m-d'),
                        'time' => $matchTime,
                        'result' => null,
                    ];
                }
            }
            $currentDate->modify($roundGap);

            // Rotate teams (keep the first team fixed)
            $lastTeam = array_pop($teams);
            array_splice($teams, 1, 0, [$lastTeam]);
        }

        // Add a gap between halves (2 periods of the defined frequency)
        $currentDate->modify($roundGap);

        // Second half: reverse fixtures
        // Reset team order for reverse rotation
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
                        $temp = $home;
                        $home = $away;
                        $away = $temp;
                    }

                    $fixtures[] = [
                        'id' => $this->generateId(),
                        'homeTeamId' => $home,
                        'awayTeamId' => $away,
                        'date' => $currentDate->format('Y-m-d'),
                        'time' => $matchTime,
                        'result' => null,
                    ];
                }
            }
            $currentDate->modify($roundGap);

            // Rotate teams (keep the first team fixed)
            $lastTeam = array_pop($teams);
            array_splice($teams, 1, 0, [$lastTeam]);
        }

        return $fixtures;
    }

    /**
     * Calculate league standings from fixture results.
     */
    public function calculateStandings(array $league, array $teams): array
    {
        $standings = [];

        // Initialise standings for each team
        foreach ($league['teamIds'] as $teamId) {
            $team = $this->findTeamById($teams, $teamId);
            $standings[$teamId] = [
                'teamId' => $teamId,
                'teamSlug' => $team['slug'] ?? $teamId,
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

        // Sort fixtures by date to ensure form is chronological
        $fixtures = $league['fixtures'] ?? [];
        usort($fixtures, function ($a, $b) {
            return strcmp($a['date'] . $a['time'], $b['date'] . $b['time']);
        });

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
                // Home Win
                $standings[$homeId]['won']++;
                $standings[$homeId]['points'] += 3;
                $standings[$homeId]['form'][] = 'W';

                // Away Loss
                $standings[$awayId]['lost']++;
                $standings[$awayId]['form'][] = 'L';
            } elseif ($awayScore > $homeScore) {
                // Away Win
                $standings[$awayId]['won']++;
                $standings[$awayId]['points'] += 3;
                $standings[$awayId]['form'][] = 'W';

                // Home Loss
                $standings[$homeId]['lost']++;
                $standings[$homeId]['form'][] = 'L';
            } else {
                // Draw
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
    private function findTeamById(array $teams, string $id): ?array
    {
        foreach ($teams as $team) {
            if ($team['id'] === $id) {
                return $team;
            }
        }
        return null;
    }

    /**
     * Update a fixture result.
     */
    public function updateFixtureResult(string $leagueId, string $fixtureId, array $result): bool
    {
        foreach ($this->data as $index => $league) {
            if ($league['id'] === $leagueId) {
                foreach ($league['fixtures'] as $fIndex => $fixture) {
                    if ($fixture['id'] === $fixtureId) {
                        $this->data[$index]['fixtures'][$fIndex]['result'] = $result;
                        $this->data[$index]['updated_at'] = date('Y-m-d H:i:s');
                        return $this->save();
                    }
                }
            }
        }
        return false;
    }

    /**
     * Update fixture date and time.
     */
    public function updateFixtureDateTime(string $leagueId, string $fixtureId, string $date, string $time): bool
    {
        foreach ($this->data as $index => $league) {
            if ($league['id'] === $leagueId) {
                foreach ($league['fixtures'] as $fIndex => $fixture) {
                    if ($fixture['id'] === $fixtureId) {
                        $this->data[$index]['fixtures'][$fIndex]['date'] = $date;
                        $this->data[$index]['fixtures'][$fIndex]['time'] = $time;
                        $this->data[$index]['updated_at'] = date('Y-m-d H:i:s');
                        return $this->save();
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get all leagues for a season.
     */
    public function getBySeasonId(string $seasonId): array
    {
        return array_values(array_filter($this->data, function ($league) use ($seasonId) {
            return ($league['seasonId'] ?? '') === $seasonId;
        }));
    }

    /**
     * Get fixture by ID within a league.
     */
    public function getFixture(string $leagueId, string $fixtureId): ?array
    {
        $league = $this->find($leagueId);
        if (!$league) {
            return null;
        }
        foreach ($league['fixtures'] ?? [] as $fixture) {
            if ($fixture['id'] === $fixtureId) {
                return $fixture;
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
