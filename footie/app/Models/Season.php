<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Season model for managing football seasons.
 * Handles CRUD operations and active season management.
 */
class Season extends Model
{
    /**
     * Get the database table name.
     */
    protected function getTableName(): string
    {
        return 'seasons';
    }

    /**
     * Get the currently active season.
     * Returns null if no season is marked as active.
     */
    public function getActive(): ?array
    {
        return $this->findWhere('is_active', 1);
    }

    /**
     * Set a season as the active season.
     * This will deactivate any other active season.
     */
    public function setActive(int|string $id): bool
    {
        // Check if season exists
        if (!$this->exists($id)) {
            return false;
        }

        // Deactivate all seasons
        $stmt = $this->db->prepare("UPDATE seasons SET is_active = 0, updated_at = NOW()");
        $stmt->execute();

        // Activate the target season
        $stmt = $this->db->prepare("UPDATE seasons SET is_active = 1, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get all seasons sorted by start date (newest first).
     */
    public function allSorted(): array
    {
        $stmt = $this->db->query("SELECT * FROM seasons ORDER BY start_date DESC");
        $seasons = $this->transformRows($stmt->fetchAll());

        // Load league and cup IDs for each season
        foreach ($seasons as &$season) {
            $season['leagueIds'] = $this->getLeagueIds($season['id']);
            $season['cupIds'] = $this->getCupIds($season['id']);
        }

        return $seasons;
    }

    /**
     * Get league IDs for a season.
     */
    private function getLeagueIds(int|string $seasonId): array
    {
        $stmt = $this->db->prepare("SELECT id FROM leagues WHERE season_id = ?");
        $stmt->execute([$seasonId]);
        return array_column($stmt->fetchAll(), 'id');
    }

    /**
     * Get cup IDs for a season.
     */
    private function getCupIds(int|string $seasonId): array
    {
        $stmt = $this->db->prepare("SELECT id FROM cups WHERE season_id = ?");
        $stmt->execute([$seasonId]);
        return array_column($stmt->fetchAll(), 'id');
    }

    /**
     * Override create to auto-generate unique slug if not provided.
     */
    public function create(array $record): array
    {
        if (isset($record['name']) && !isset($record['slug'])) {
            $record['slug'] = $this->generateUniqueSlug($record['name']);
        }

        return parent::create($record);
    }

    /**
     * Check if a season with the given ID already exists.
     */
    public function idExists(int|string $id): bool
    {
        return $this->exists($id);
    }

    /**
     * Create a season with a specific ID (used when importing or when ID is user-provided).
     */
    public function createWithId(array $record): array
    {
        $table = $this->getTableName();


        // Auto-generate slug if not provided
        if (isset($record['name']) && !isset($record['slug'])) {
            $record['slug'] = $this->generateUniqueSlug($record['name']);
        }

        $record['created_at'] = date('Y-m-d H:i:s');
        $record['updated_at'] = date('Y-m-d H:i:s');

        $fields = array_keys($record);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $table,
            implode(', ', array_map(fn($f) => "`{$f}`", $fields)),
            implode(', ', $placeholders)
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($record));

        return $this->find($record['id']);
    }

    /**
     * Override update to update unique slug if name changes.
     */
    public function update(int|string $id, array $updates): bool
    {
        if (isset($updates['name'])) {
            $updates['slug'] = $this->generateUniqueSlug($updates['name'], $id);
        }

        return parent::update($id, $updates);
    }

    /**
     * Get leagues for this season.
     */
    public function getLeagues(int|string $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM leagues WHERE season_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * Get cups for this season.
     */
    public function getCups(int|string $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM cups WHERE season_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * Add league to season (no-op since relationship is in leagues table).
     * League is automatically associated via its season_id foreign key.
     */
    public function addLeague(int|string $seasonId, int|string $leagueId): void
    {
        // No-op: league's season_id is already set when league is created
    }

    /**
     * Add cup to season (no-op since relationship is in cups table).
     * Cup is automatically associated via its season_id foreign key.
     */
    public function addCup(int|string $seasonId, int|string $cupId): void
    {
        // No-op: cup's season_id is already set when cup is created
    }

    /**
     * Remove league from season (no-op since relationship is in leagues table).
     * League's season_id should be updated separately if needed.
     */
    public function removeLeague(int|string $seasonId, int|string $leagueId): void
    {
        // No-op: controllers should update league's season_id directly
    }

    /**
     * Remove cup from season (no-op since relationship is in cups table).
     * Cup's season_id should be updated separately if needed.
     */
    public function removeCup(int|string $seasonId, int|string $cupId): void
    {
        // No-op: controllers should update cup's season_id directly
    }
}
