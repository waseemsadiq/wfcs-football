<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * FixturePhoto model for managing fixture photo galleries.
 */
class FixturePhoto extends Model
{
    /**
     * Get the database table name.
     */
    protected function getTableName(): string
    {
        return 'fixture_photos';
    }

    /**
     * Get all photos for a specific fixture.
     */
    public function getByFixture(string $fixtureType, int $fixtureId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM fixture_photos
             WHERE fixture_type = ? AND fixture_id = ?
             ORDER BY sort_order ASC, created_at ASC"
        );
        $stmt->execute([$fixtureType, $fixtureId]);

        return $this->transformRows($stmt->fetchAll());
    }

    /**
     * Create a new fixture photo record.
     */
    public function create(array $record): array
    {
        if (!isset($record['created_at'])) {
            $record['created_at'] = date('Y-m-d H:i:s');
        }

        return parent::create($record);
    }

    /**
     * Delete a photo and return its file path for cleanup.
     */
    public function deletePhoto(int $id): ?string
    {
        $photo = $this->find($id);
        if (!$photo) {
            return null;
        }

        $filePath = $photo['filePath'];
        $this->delete($id);

        return $filePath;
    }

    /**
     * Get the maximum sort order for a fixture.
     */
    public function getMaxSortOrder(string $fixtureType, int $fixtureId): int
    {
        $stmt = $this->db->prepare(
            "SELECT MAX(sort_order) as max_order
             FROM fixture_photos
             WHERE fixture_type = ? AND fixture_id = ?"
        );
        $stmt->execute([$fixtureType, $fixtureId]);
        $result = $stmt->fetch();

        return (int) ($result['max_order'] ?? 0);
    }
}
