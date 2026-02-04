<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Team model for managing football teams.
 * Handles CRUD operations for team data stored in JSON.
 */
class Team extends Model
{
    /**
     * Get the path to the teams JSON data file.
     */
    protected function getDataPath(): string
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        return $config['data_path'] . '/teams/teams.json';
    }

    /**
     * Create a new team with the given data.
     * Ensures players array is properly formatted.
     */
    public function create(array $record): array
    {
        // Ensure players is an array
        if (!isset($record['players'])) {
            $record['players'] = [];
        }

        if (isset($record['name']) && !isset($record['slug'])) {
            $record['slug'] = self::slugify($record['name']);
        }

        return parent::create($record);
    }

    /**
     * Update a team by ID.
     * Ensures players array is properly formatted.
     */
    public function update(string $id, array $updates): bool
    {
        // Ensure players remains an array if provided
        if (isset($updates['players']) && is_string($updates['players'])) {
            $updates['players'] = $this->parsePlayersFromText($updates['players']);
        }

        if (isset($updates['name'])) {
            $updates['slug'] = self::slugify($updates['name']);
        }

        return parent::update($id, $updates);
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
        $teams = $this->all();
        usort($teams, fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));
        return $teams;
    }

    /**
     * Search teams by name.
     */
    public function search(string $query): array
    {
        $query = strtolower($query);
        return array_filter($this->data, function ($team) use ($query) {
            return str_contains(strtolower($team['name'] ?? ''), $query);
        });
    }

    /**
     * Get the count of players for a team.
     */
    public function playerCount(array $team): int
    {
        return count($team['players'] ?? []);
    }
}
