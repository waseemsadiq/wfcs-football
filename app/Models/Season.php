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
     * Get the path to the seasons JSON data file.
     */
    protected function getDataPath(): string
    {
        return dirname(__DIR__, 2) . '/data/seasons/seasons.json';
    }

    /**
     * Create a new season with custom ID.
     * Seasons use a format like "2024-25" as the ID.
     */
    public function createWithId(array $data): array
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Set default values for arrays if not provided
        if (!isset($data['leagueIds'])) {
            $data['leagueIds'] = [];
        }
        if (!isset($data['cupIds'])) {
            $data['cupIds'] = [];
        }
        if (!isset($data['isActive'])) {
            $data['isActive'] = false;
        }

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = self::slugify($data['name']);
        }

        $this->data[] = $data;
        $this->save();

        return $data;
    }

    /**
     * Get the currently active season.
     * Returns null if no season is marked as active.
     */
    public function getActive(): ?array
    {
        foreach ($this->data as $season) {
            if (isset($season['isActive']) && $season['isActive'] === true) {
                return $season;
            }
        }
        return null;
    }

    /**
     * Set a season as the active season.
     * This will deactivate any other active season.
     */
    public function setActive(string $id): bool
    {
        $found = false;

        foreach ($this->data as $index => $season) {
            if ($season['id'] === $id) {
                $this->data[$index]['isActive'] = true;
                $this->data[$index]['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
            } else {
                $this->data[$index]['isActive'] = false;
                $this->data[$index]['updated_at'] = date('Y-m-d H:i:s');
            }
        }

        if ($found) {
            return $this->save();
        }

        return false;
    }

    /**
     * Check if a season ID already exists.
     */
    public function idExists(string $id): bool
    {
        return $this->find($id) !== null;
    }

    /**
     * Get all seasons sorted by start date (newest first).
     */
    public function allSorted(): array
    {
        $seasons = $this->data;
        usort($seasons, function ($a, $b) {
            return strcmp($b['startDate'] ?? '', $a['startDate'] ?? '');
        });
        return $seasons;
    }

    /**
     * Add a league to a season.
     */
    public function addLeague(string $seasonId, string $leagueId): bool
    {
        foreach ($this->data as $index => $season) {
            if ($season['id'] === $seasonId) {
                if (!in_array($leagueId, $this->data[$index]['leagueIds'])) {
                    $this->data[$index]['leagueIds'][] = $leagueId;
                    $this->data[$index]['updated_at'] = date('Y-m-d H:i:s');
                    return $this->save();
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Add a cup to a season.
     */
    public function addCup(string $seasonId, string $cupId): bool
    {
        foreach ($this->data as $index => $season) {
            if ($season['id'] === $seasonId) {
                if (!in_array($cupId, $this->data[$index]['cupIds'])) {
                    $this->data[$index]['cupIds'][] = $cupId;
                    $this->data[$index]['updated_at'] = date('Y-m-d H:i:s');
                    return $this->save();
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Remove a league from a season.
     */
    public function removeLeague(string $seasonId, string $leagueId): bool
    {
        foreach ($this->data as $index => $season) {
            if ($season['id'] === $seasonId) {
                if (($key = array_search($leagueId, $this->data[$index]['leagueIds'])) !== false) {
                    unset($this->data[$index]['leagueIds'][$key]);
                    // Re-index array
                    $this->data[$index]['leagueIds'] = array_values($this->data[$index]['leagueIds']);
                    $this->data[$index]['updated_at'] = date('Y-m-d H:i:s');
                    return $this->save();
                }
                return true; // Already removed
            }
        }
        return false;
    }

    /**
     * Remove a cup from a season.
     */
    public function removeCup(string $seasonId, string $cupId): bool
    {
        foreach ($this->data as $index => $season) {
            if ($season['id'] === $seasonId) {
                if (($key = array_search($cupId, $this->data[$index]['cupIds'])) !== false) {
                    unset($this->data[$index]['cupIds'][$key]);
                    // Re-index array
                    $this->data[$index]['cupIds'] = array_values($this->data[$index]['cupIds']);
                    $this->data[$index]['updated_at'] = date('Y-m-d H:i:s');
                    return $this->save();
                }
                return true; // Already removed
            }
        }
        return false;
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
