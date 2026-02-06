<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Player model for managing football players.
 * Handles CRUD operations, statistics, and pool players.
 */
class Player extends Model
{
    /**
     * Get the database table name.
     */
    protected function getTableName(): string
    {
        return 'players';
    }

    /**
     * Create a new player with the given data.
     */
    public function create(array $record): array
    {
        // Auto-generate unique slug if not provided
        if (isset($record['name']) && !isset($record['slug'])) {
            $record['slug'] = $this->generateUniqueSlug($record['name']);
        }

        // Set timestamps
        if (!isset($record['created_at'])) {
            $record['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($record['updated_at'])) {
            $record['updated_at'] = date('Y-m-d H:i:s');
        }

        return parent::create($record);
    }

    /**
     * Update a player by ID.
     */
    public function update(int|string $id, array $data): bool
    {
        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        return parent::update($id, $data);
    }

    /**
     * Get all players for a specific team.
     */
    public function getByTeam(int|string $teamId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM players WHERE team_id = ? ORDER BY squad_number ASC, name ASC"
        );
        $stmt->execute([$teamId]);

        return $this->transformRows($stmt->fetchAll());
    }

    /**
     * Get all pool players (no team assignment).
     */
    public function getPoolPlayers(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM players WHERE is_pool_player = 1 OR team_id IS NULL ORDER BY name ASC"
        );

        return $this->transformRows($stmt->fetchAll());
    }

    /**
     * Get player statistics.
     */
    public function getStats(int|string $playerId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM player_stats WHERE player_id = ?");
        $stmt->execute([$playerId]);

        $result = $stmt->fetch();
        return $result ? $this->transformKeys($result) : [
            'playerId' => $playerId,
            'totalGoals' => 0,
            'totalAssists' => 0,
            'yellowCards' => 0,
            'redCards' => 0,
            'matchesPlayed' => 0,
        ];
    }

    /**
     * Get player with their team information.
     */
    public function getWithTeam(int|string $playerId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, t.name as team_name, t.slug as team_slug
            FROM players p
            LEFT JOIN teams t ON p.team_id = t.id
            WHERE p.id = ?
        ");
        $stmt->execute([$playerId]);

        $result = $stmt->fetch();
        return $result ? $this->transformKeys($result) : null;
    }

    /**
     * Get match events for a player.
     */
    public function getEvents(int|string $playerId): array
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
     * Check if player is available (not injured, suspended, unavailable).
     */
    public function isAvailable(int|string $playerId): bool
    {
        $player = $this->find($playerId);
        return $player && $player['status'] === 'active';
    }

    /**
     * Validate squad number uniqueness per team.
     */
    public function isSquadNumberAvailable(int $squadNumber, int|string $teamId, ?int $excludePlayerId = null): bool
    {
        $query = "SELECT COUNT(*) as count FROM players WHERE squad_number = ? AND team_id = ?";
        $params = [$squadNumber, $teamId];

        if ($excludePlayerId) {
            $query .= " AND id != ?";
            $params[] = $excludePlayerId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        $result = $stmt->fetch();
        return $result['count'] == 0;
    }

    /**
     * Assign pool player to a team.
     */
    public function assignToTeam(int|string $playerId, int|string $teamId): bool
    {
        return $this->update($playerId, [
            'team_id' => $teamId,
            'is_pool_player' => 0,
        ]);
    }

    /**
     * Release player from team (make pool player).
     */
    public function releaseFromTeam(int|string $playerId): bool
    {
        return $this->update($playerId, [
            'team_id' => null,
            'is_pool_player' => 1,
            'squad_number' => null,
        ]);
    }

    /**
     * Get top scorers across all competitions or specific league.
     */
    public function getTopScorers(int $limit = 10, ?int $leagueId = null): array
    {
        if ($leagueId) {
            // Get top scorers for specific league
            $stmt = $this->db->prepare("
                SELECT p.*, ps.total_goals, t.name as team_name
                FROM players p
                INNER JOIN player_stats ps ON p.id = ps.player_id
                LEFT JOIN teams t ON p.team_id = t.id
                WHERE ps.total_goals > 0
                ORDER BY ps.total_goals DESC, p.name ASC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
        } else {
            // Get top scorers overall
            $stmt = $this->db->prepare("
                SELECT p.*, ps.total_goals, t.name as team_name
                FROM players p
                INNER JOIN player_stats ps ON p.id = ps.player_id
                LEFT JOIN teams t ON p.team_id = t.id
                WHERE ps.total_goals > 0
                ORDER BY ps.total_goals DESC, p.name ASC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
        }

        return $this->transformRows($stmt->fetchAll());
    }
}
