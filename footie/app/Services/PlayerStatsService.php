<?php

declare(strict_types=1);

namespace App\Services;

use Core\Database;
use PDO;

/**
 * PlayerStatsService - Calculate and cache player statistics.
 * Aggregates data from match_events table into player_stats for performance.
 */
class PlayerStatsService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Recalculate statistics for a single player.
     */
    public function recalculatePlayerStats(int $playerId): bool
    {
        try {
            // Get player's team
            $playerStmt = $this->db->prepare("SELECT team_id FROM players WHERE id = ?");
            $playerStmt->execute([$playerId]);
            $player = $playerStmt->fetch();

            if (!$player) {
                return false;
            }

            // Count goals
            $goalsStmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM match_events
                WHERE player_id = ? AND event_type = 'goal'
            ");
            $goalsStmt->execute([$playerId]);
            $goals = (int) $goalsStmt->fetch()['count'];

            // Count assists
            $assistsStmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM match_events
                WHERE player_id = ? AND event_type = 'assist'
            ");
            $assistsStmt->execute([$playerId]);
            $assists = (int) $assistsStmt->fetch()['count'];

            // Count yellow cards
            $yellowStmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM match_events
                WHERE player_id = ? AND event_type = 'yellow_card'
            ");
            $yellowStmt->execute([$playerId]);
            $yellowCards = (int) $yellowStmt->fetch()['count'];

            // Count red cards
            $redStmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM match_events
                WHERE player_id = ? AND event_type = 'red_card'
            ");
            $redStmt->execute([$playerId]);
            $redCards = (int) $redStmt->fetch()['count'];

            // Count blue cards
            $blueStmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM match_events
                WHERE player_id = ? AND event_type = 'blue_card'
            ");
            $blueStmt->execute([$playerId]);
            $blueCards = (int) $blueStmt->fetch()['count'];

            // Count sin bins
            $sinBinStmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM match_events
                WHERE player_id = ? AND event_type = 'sin_bin'
            ");
            $sinBinStmt->execute([$playerId]);
            $sinBins = (int) $sinBinStmt->fetch()['count'];

            // Count unique matches played (distinct fixture combinations)
            $matchesStmt = $this->db->prepare("
                SELECT COUNT(DISTINCT CONCAT(fixture_type, '-', fixture_id)) as count
                FROM match_events
                WHERE player_id = ?
            ");
            $matchesStmt->execute([$playerId]);
            $matchesPlayed = (int) $matchesStmt->fetch()['count'];

            // Upsert into player_stats
            $upsertStmt = $this->db->prepare("
                INSERT INTO player_stats
                (player_id, team_id, total_goals, total_assists, yellow_cards, red_cards, blue_cards, sin_bins, matches_played, last_updated)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    team_id = VALUES(team_id),
                    total_goals = VALUES(total_goals),
                    total_assists = VALUES(total_assists),
                    yellow_cards = VALUES(yellow_cards),
                    red_cards = VALUES(red_cards),
                    blue_cards = VALUES(blue_cards),
                    sin_bins = VALUES(sin_bins),
                    matches_played = VALUES(matches_played),
                    last_updated = NOW()
            ");

            return $upsertStmt->execute([
                $playerId,
                $player['team_id'],
                $goals,
                $assists,
                $yellowCards,
                $redCards,
                $blueCards,
                $sinBins,
                $matchesPlayed,
            ]);
        } catch (\Exception $e) {
            error_log("Failed to recalculate player stats: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recalculate statistics for all players in a team.
     */
    public function recalculateTeamStats(int $teamId): bool
    {
        try {
            // Get all players for the team
            $stmt = $this->db->prepare("SELECT id FROM players WHERE team_id = ?");
            $stmt->execute([$teamId]);
            $players = $stmt->fetchAll();

            foreach ($players as $player) {
                $this->recalculatePlayerStats((int) $player['id']);
            }

            return true;
        } catch (\Exception $e) {
            error_log("Failed to recalculate team stats: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recalculate statistics for all players (full rebuild).
     * Used after data migration or major changes.
     */
    public function recalculateAllStats(): bool
    {
        try {
            // Get all players
            $stmt = $this->db->query("SELECT id FROM players");
            $players = $stmt->fetchAll();

            $count = 0;
            foreach ($players as $player) {
                if ($this->recalculatePlayerStats((int) $player['id'])) {
                    $count++;
                }
            }

            error_log("Recalculated stats for {$count} players");
            return true;
        } catch (\Exception $e) {
            error_log("Failed to recalculate all stats: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recalculate stats for players involved in a specific fixture.
     */
    public function recalculateFixtureStats(string $fixtureType, int $fixtureId): bool
    {
        try {
            // Get all players with events in this fixture
            $stmt = $this->db->prepare("
                SELECT DISTINCT player_id
                FROM match_events
                WHERE fixture_type = ? AND fixture_id = ? AND player_id IS NOT NULL
            ");
            $stmt->execute([$fixtureType, $fixtureId]);
            $players = $stmt->fetchAll();

            foreach ($players as $player) {
                $this->recalculatePlayerStats((int) $player['player_id']);
            }

            return true;
        } catch (\Exception $e) {
            error_log("Failed to recalculate fixture stats: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get league-specific statistics for a player.
     * Not cached - calculated on demand.
     */
    public function getLeagueStats(int $playerId, int $leagueId): array
    {
        // Get all league fixtures
        $fixturesStmt = $this->db->prepare("SELECT id FROM league_fixtures WHERE league_id = ?");
        $fixturesStmt->execute([$leagueId]);
        $fixtures = $fixturesStmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($fixtures)) {
            return [
                'goals' => 0,
                'assists' => 0,
                'yellowCards' => 0,
                'redCards' => 0,
                'blueCards' => 0,
                'sinBins' => 0,
                'matchesPlayed' => 0,
            ];
        }

        $placeholders = implode(',', array_fill(0, count($fixtures), '?'));

        // Count events by type
        $stmt = $this->db->prepare("
            SELECT
                event_type,
                COUNT(*) as count,
                COUNT(DISTINCT fixture_id) as matches
            FROM match_events
            WHERE player_id = ?
              AND fixture_type = 'league'
              AND fixture_id IN ({$placeholders})
            GROUP BY event_type
        ");

        $params = array_merge([$playerId], $fixtures);
        $stmt->execute($params);

        $stats = [
            'goals' => 0,
            'assists' => 0,
            'yellowCards' => 0,
            'redCards' => 0,
            'blueCards' => 0,
            'sinBins' => 0,
            'matchesPlayed' => 0,
        ];

        while ($row = $stmt->fetch()) {
            switch ($row['event_type']) {
                case 'goal':
                    $stats['goals'] = (int) $row['count'];
                    $stats['matchesPlayed'] = max($stats['matchesPlayed'], (int) $row['matches']);
                    break;
                case 'assist':
                    $stats['assists'] = (int) $row['count'];
                    break;
                case 'yellow_card':
                    $stats['yellowCards'] = (int) $row['count'];
                    break;
                case 'red_card':
                    $stats['redCards'] = (int) $row['count'];
                    break;
                case 'blue_card':
                    $stats['blueCards'] = (int) $row['count'];
                    break;
                case 'sin_bin':
                    $stats['sinBins'] = (int) $row['count'];
                    break;
            }
        }

        return $stats;
    }
}
