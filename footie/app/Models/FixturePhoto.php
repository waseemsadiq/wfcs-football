<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * FixturePhoto model for managing match photo galleries.
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
     * Create a new photo record.
     */
    public function create(array $record): array
    {
        // Set timestamp
        if (!isset($record['created_at'])) {
            $record['created_at'] = date('Y-m-d H:i:s');
        }

        return parent::create($record);
    }

    /**
     * Delete photo and its file.
     */
    public function deletePhoto(int $id): bool
    {
        $photo = $this->find($id);
        if (!$photo) {
            return false;
        }

        // Delete file from filesystem
        $filePath = __DIR__ . '/../../uploads/fixtures/' . $photo['filePath'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $this->delete($id);
    }

    /**
     * Update sort order for multiple photos.
     */
    public function updateSortOrder(array $photoIds): bool
    {
        foreach ($photoIds as $order => $photoId) {
            $this->update($photoId, ['sort_order' => $order]);
        }

        return true;
    }
}
