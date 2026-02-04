<?php

declare(strict_types=1);

namespace App\Models\Traits;

/**
 * HasTeams Trait
 *
 * Provides team association management for competitions (leagues, cups).
 * Handles many-to-many relationships through join tables.
 */
trait HasTeams
{
    /**
     * Get the join table name for team associations.
     * Must be implemented by the using class.
     */
    abstract protected function getTeamJoinTable(): string;

    /**
     * Get the competition ID column name in the join table.
     * Must be implemented by the using class.
     */
    abstract protected function getCompetitionIdColumn(): string;

    /**
     * Get team IDs associated with a competition.
     */
    public function getTeamIds(int|string $id): array
    {
        $table = $this->getTeamJoinTable();
        $column = $this->getCompetitionIdColumn();

        $stmt = $this->db->prepare("SELECT team_id FROM {$table} WHERE {$column} = ?");
        $stmt->execute([$id]);

        return array_column($stmt->fetchAll(), 'team_id');
    }

    /**
     * Set teams for a competition.
     * Replaces all existing team associations.
     */
    public function setTeams(int|string $id, array $teamIds): void
    {
        $table = $this->getTeamJoinTable();
        $column = $this->getCompetitionIdColumn();

        // Delete existing associations
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE {$column} = ?");
        $stmt->execute([$id]);

        // Insert new associations
        if (!empty($teamIds)) {
            $stmt = $this->db->prepare("INSERT INTO {$table} ({$column}, team_id) VALUES (?, ?)");
            foreach ($teamIds as $teamId) {
                $stmt->execute([$id, $teamId]);
            }
        }
    }

    /**
     * Add a team to a competition.
     */
    public function addTeam(int|string $id, int|string $teamId): void
    {
        $table = $this->getTeamJoinTable();
        $column = $this->getCompetitionIdColumn();

        $stmt = $this->db->prepare("
            INSERT IGNORE INTO {$table} ({$column}, team_id)
            VALUES (?, ?)
        ");
        $stmt->execute([$id, $teamId]);
    }

    /**
     * Remove a team from a competition.
     */
    public function removeTeam(int|string $id, int|string $teamId): void
    {
        $table = $this->getTeamJoinTable();
        $column = $this->getCompetitionIdColumn();

        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE {$column} = ? AND team_id = ?");
        $stmt->execute([$id, $teamId]);
    }

    /**
     * Check if a competition has a specific team.
     */
    public function hasTeam(int|string $id, int|string $teamId): bool
    {
        $table = $this->getTeamJoinTable();
        $column = $this->getCompetitionIdColumn();

        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM {$table}
            WHERE {$column} = ? AND team_id = ?
        ");
        $stmt->execute([$id, $teamId]);

        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Get the count of teams in a competition.
     */
    public function getTeamsCount(int|string $id): int
    {
        $table = $this->getTeamJoinTable();
        $column = $this->getCompetitionIdColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = ?");
        $stmt->execute([$id]);

        return (int)$stmt->fetchColumn();
    }
}
