<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * TeamStaff model for managing team support staff.
 * Handles coaches, managers, and contacts.
 */
class TeamStaff extends Model
{
    /**
     * Get the database table name.
     */
    protected function getTableName(): string
    {
        return 'team_staff';
    }

    /**
     * Create a new staff member with the given data.
     */
    public function create(array $record): array
    {
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
     * Update a staff member by ID.
     */
    public function update(int|string $id, array $data): bool
    {
        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        return parent::update($id, $data);
    }

    /**
     * Get all staff for a specific team.
     */
    public function getByTeam(int|string $teamId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM team_staff WHERE team_id = ? ORDER BY role ASC, name ASC"
        );
        $stmt->execute([$teamId]);

        return $this->transformRows($stmt->fetchAll());
    }

    public function getByRole(string $role): array
    {
        $stmt = $this->db->prepare(
            "SELECT ts.*, t.name as team_name
             FROM team_staff ts
             LEFT JOIN teams t ON ts.team_id = t.id
             WHERE ts.role LIKE ?
             ORDER BY t.name ASC, ts.name ASC"
        );
        $stmt->execute(['%' . $role . '%']);

        return $this->transformRows($stmt->fetchAll());
    }

    public function getWithTeam(int|string $staffId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT ts.*, t.name as team_name, t.slug as team_slug
            FROM team_staff ts
            LEFT JOIN teams t ON ts.team_id = t.id
            WHERE ts.id = ?
        ");
        $stmt->execute([$staffId]);

        $result = $stmt->fetch();
        return $result ? $this->transformKeys($result) : null;
    }

    public function isValidRole(string|array $role): bool
    {
        $validRoles = array_keys(self::getValidRoles());

        if (is_array($role)) {
            foreach ($role as $r) {
                if (!in_array($r, $validRoles)) {
                    return false;
                }
            }
            return !empty($role);
        }

        return in_array($role, $validRoles);
    }

    /**
     * Get valid staff roles with labels.
     */
    public static function getValidRoles(): array
    {
        return [
            'coach' => 'Coach',
            'assistant_coach' => 'Assistant Coach',
            'manager' => 'Manager',
            'referee' => 'Referee',
            'contact' => 'Contact',
            'other' => 'Other',
        ];
    }

    public static function formatRole(string|array $role): string
    {
        $roles = self::getValidRoles();

        if (is_string($role) && strpos($role, ',') !== false) {
            $role = explode(',', $role);
        }

        if (is_array($role)) {
            $labels = array_map(fn($r) => $roles[$r] ?? ucfirst(str_replace('_', ' ', $r)), $role);
            return implode(', ', $labels);
        }

        return $roles[$role] ?? ucfirst(str_replace('_', ' ', $role));
    }
}
