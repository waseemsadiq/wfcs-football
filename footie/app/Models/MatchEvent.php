<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * MatchEvent model for managing match events (goals, cards, assists).
 * Replaces JSON TEXT fields with structured relational data.
 */
class MatchEvent extends Model
{
    /**
     * Get the database table name.
     */
    protected function getTableName(): string
    {
        return 'match_events';
    }

    /**
     * Get all events for a specific fixture.
     */
    public function getByFixture(string $fixtureType, int|string $fixtureId): array
    {
        $stmt = $this->db->prepare("
            SELECT me.*, p.name as player_name, t.name as team_name
            FROM match_events me
            LEFT JOIN players p ON me.player_id = p.id
            INNER JOIN teams t ON me.team_id = t.id
            WHERE me.fixture_type = ? AND me.fixture_id = ?
            ORDER BY me.minute ASC, me.created_at ASC
        ");
        $stmt->execute([$fixtureType, $fixtureId]);

        return $this->transformRows($stmt->fetchAll());
    }

    /**
     * Get all events for a specific player.
     */
    public function getByPlayer(int|string $playerId): array
    {
        $stmt = $this->db->prepare("
            SELECT me.*, t.name as team_name
            FROM match_events me
            INNER JOIN teams t ON me.team_id = t.id
            WHERE me.player_id = ?
            ORDER BY me.created_at DESC
        ");
        $stmt->execute([$playerId]);

        return $this->transformRows($stmt->fetchAll());
    }

    /**
     * Get all events for a specific team.
     */
    public function getByTeam(int|string $teamId): array
    {
        $stmt = $this->db->prepare("
            SELECT me.*, p.name as player_name
            FROM match_events me
            LEFT JOIN players p ON me.player_id = p.id
            WHERE me.team_id = ?
            ORDER BY me.created_at DESC
        ");
        $stmt->execute([$teamId]);

        return $this->transformRows($stmt->fetchAll());
    }

    /**
     * Get goal scorers for a fixture (grouped by team).
     */
    public function getGoalScorers(string $fixtureType, int|string $fixtureId): array
    {
        $stmt = $this->db->prepare("
            SELECT me.*, p.name as player_name, t.name as team_name
            FROM match_events me
            LEFT JOIN players p ON me.player_id = p.id
            INNER JOIN teams t ON me.team_id = t.id
            WHERE me.fixture_type = ? AND me.fixture_id = ? AND me.event_type = 'goal'
            ORDER BY me.minute ASC
        ");
        $stmt->execute([$fixtureType, $fixtureId]);

        return $this->transformRows($stmt->fetchAll());
    }

    /**
     * Get card recipients for a fixture.
     */
    public function getCardRecipients(string $fixtureType, int|string $fixtureId): array
    {
        $stmt = $this->db->prepare("
            SELECT me.*, p.name as player_name, t.name as team_name
            FROM match_events me
            LEFT JOIN players p ON me.player_id = p.id
            INNER JOIN teams t ON me.team_id = t.id
            WHERE me.fixture_type = ? AND me.fixture_id = ?
              AND me.event_type IN ('yellow_card', 'red_card')
            ORDER BY me.minute ASC
        ");
        $stmt->execute([$fixtureType, $fixtureId]);

        return $this->transformRows($stmt->fetchAll());
    }

    /**
     * Replace all events for a fixture (delete old, insert new).
     * Used when updating fixture results.
     */
    public function replaceFixtureEvents(string $fixtureType, int|string $fixtureId, array $events): bool
    {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Delete existing events
            $stmt = $this->db->prepare(
                "DELETE FROM match_events WHERE fixture_type = ? AND fixture_id = ?"
            );
            $stmt->execute([$fixtureType, $fixtureId]);

            // Insert new events
            if (!empty($events)) {
                $stmt = $this->db->prepare("
                    INSERT INTO match_events
                    (fixture_type, fixture_id, team_id, player_id, event_type, minute, notes, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");

                foreach ($events as $event) {
                    $stmt->execute([
                        $fixtureType,
                        $fixtureId,
                        $event['teamId'] ?? $event['team_id'],
                        $event['playerId'] ?? $event['player_id'] ?? null,
                        $event['eventType'] ?? $event['event_type'],
                        $event['minute'] ?? null,
                        $event['notes'] ?? null,
                    ]);
                }
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback on error
            $this->db->rollBack();
            error_log("Failed to replace fixture events: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete all events for a fixture.
     */
    public function deleteFixtureEvents(string $fixtureType, int|string $fixtureId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM match_events WHERE fixture_type = ? AND fixture_id = ?"
        );
        return $stmt->execute([$fixtureType, $fixtureId]);
    }

    /**
     * Count events by type for a player.
     */
    public function countPlayerEvents(int|string $playerId, string $eventType): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM match_events
            WHERE player_id = ? AND event_type = ?
        ");
        $stmt->execute([$playerId, $eventType]);

        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }
}
