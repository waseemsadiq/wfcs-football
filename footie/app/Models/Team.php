<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Team model for managing football teams.
 * Handles CRUD operations for teams and their players.
 */
class Team extends Model
{
    /**
     * Get the database table name.
     */
    protected function getTableName(): string
    {
        return 'teams';
    }

    /**
     * Create a new team with the given data.
     * Handles players if provided in the record.
     */
    public function create(array $record): array
    {
        // Extract players if provided
        $players = $record['players'] ?? [];
        unset($record['players']);

        // Auto-generate unique slug if not provided
        if (isset($record['name']) && !isset($record['slug'])) {
            $record['slug'] = $this->generateUniqueSlug($record['name']);
        }

        // Create the team
        $team = parent::create($record);

        // Add players if provided
        if (!empty($players)) {
            $this->setPlayers($team['id'], $players);
        }

        return $team;
    }

    /**
     * Update a team by ID.
     * Handles players if provided in the updates.
     */
    public function update(int|string $id, array $updates): bool
    {
        // Extract players if provided
        $players = null;
        if (isset($updates['players'])) {
            $players = $updates['players'];

            // Convert text to array if needed
            if (is_string($players)) {
                $players = $this->parsePlayersFromText($players);
            }

            unset($updates['players']);
        }

        // Auto-generate unique slug if name changes (exclude current team)
        if (isset($updates['name'])) {
            $updates['slug'] = $this->generateUniqueSlug($updates['name'], $id);
        }

        // Update the team
        $result = parent::update($id, $updates);

        // Update players if provided
        if ($players !== null) {
            $this->setPlayers($id, $players);
        }

        return $result;
    }

    /**
     * Override find to include players.
     */
    public function find(int|string $id): ?array
    {
        $team = parent::find($id);

        if ($team) {
            $team['players'] = $this->getPlayers($id);
        }

        return $team;
    }

    /**
     * Override all to include players for each team.
     */
    public function all(): array
    {
        $teams = parent::all();

        foreach ($teams as &$team) {
            $team['players'] = $this->getPlayers($team['id']);
        }

        return $teams;
    }

    /**
     * Override findWhere to include players.
     */
    public function findWhere(string $field, mixed $value): ?array
    {
        $team = parent::findWhere($field, $value);

        if ($team) {
            $team['players'] = $this->getPlayers($team['id']);
        }

        return $team;
    }

    /**
     * Get players for a team.
     */
    public function getPlayers(int|string $teamId): array
    {
        $stmt = $this->db->prepare("SELECT name FROM players WHERE team_id = ? ORDER BY name");
        $stmt->execute([$teamId]);

        // Return array of player names
        return array_column($stmt->fetchAll(), 'name');
    }

    /**
     * Set players for a team (replaces existing players).
     */
    public function setPlayers(int|string $teamId, array $players): void
    {
        // Delete existing players
        $stmt = $this->db->prepare("DELETE FROM players WHERE team_id = ?");
        $stmt->execute([$teamId]);

        // Insert new players
        if (!empty($players)) {
            $stmt = $this->db->prepare("INSERT INTO players (team_id, name) VALUES (?, ?)");

            foreach ($players as $player) {
                $name = is_string($player) ? trim($player) : '';
                if ($name !== '') {
                    $stmt->execute([$teamId, $name]);
                }
            }
        }
    }

    /**
     * Parse a text block of player names (one per line) into an array.
     */
    public function parsePlayersFromText(string $text): array
    {
        $lines = explode("\n", $text);
        $players = [];

        foreach ($lines as $line) {
            $name = trim($line);
            if ($name !== '') {
                $players[] = $name;
            }
        }

        return $players;
    }

    /**
     * Convert a players array to text format (one per line).
     */
    public function playersToText(array $players): string
    {
        return implode("\n", $players);
    }

    /**
     * Get all teams sorted by name.
     */
    public function allSorted(): array
    {
        $stmt = $this->db->query("SELECT * FROM teams ORDER BY name");
        $teams = $stmt->fetchAll();

        foreach ($teams as &$team) {
            $team['players'] = $this->getPlayers($team['id']);
        }

        return $teams;
    }

    /**
     * Search teams by name.
     */
    public function search(string $query): array
    {
        $stmt = $this->db->prepare("SELECT * FROM teams WHERE name LIKE ? ORDER BY name");
        $stmt->execute(['%' . $query . '%']);
        $teams = $stmt->fetchAll();

        foreach ($teams as &$team) {
            $team['players'] = $this->getPlayers($team['id']);
        }

        return $teams;
    }

    /**
     * Get the count of players for a team.
     */
    public function playerCount(array $team): int
    {
        return count($team['players'] ?? []);
    }
}
