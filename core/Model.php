<?php

declare(strict_types=1);

namespace Core;

/**
 * Base model for JSON file-based data storage.
 * Provides CRUD operations for entities stored in JSON files.
 */
abstract class Model
{
    protected string $dataFile;
    protected array $data = [];

    public function __construct()
    {
        $this->load();
    }

    /**
     * Get the path to the JSON data file.
     * Child classes must implement this.
     */
    abstract protected function getDataPath(): string;

    /**
     * Load data from the JSON file.
     */
    protected function load(): void
    {
        $this->dataFile = $this->getDataPath();

        if (file_exists($this->dataFile)) {
            $content = file_get_contents($this->dataFile);
            $this->data = json_decode($content, true) ?: [];
        } else {
            $this->data = [];
        }
    }

    /**
     * Save data to the JSON file.
     * Uses file locking to prevent race conditions.
     */
    protected function save(): bool
    {
        $dir = dirname($this->dataFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $json = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($this->dataFile, $json, LOCK_EX) !== false;
    }

    /**
     * Get all records.
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Find a record by ID.
     */
    public function find(string $id): ?array
    {
        foreach ($this->data as $record) {
            if (isset($record['id']) && $record['id'] === $id) {
                return $record;
            }
        }
        return null;
    }

    /**
     * Create a new record.
     * Returns the created record with generated ID.
     */
    public function create(array $record): array
    {
        $record['id'] = $this->generateId();
        $record['created_at'] = date('Y-m-d H:i:s');
        $record['updated_at'] = date('Y-m-d H:i:s');

        $this->data[] = $record;
        $this->save();

        return $record;
    }

    /**
     * Update an existing record.
     * Returns true on success, false if record not found.
     */
    public function update(string $id, array $updates): bool
    {
        foreach ($this->data as $index => $record) {
            if (isset($record['id']) && $record['id'] === $id) {
                $updates['updated_at'] = date('Y-m-d H:i:s');
                $this->data[$index] = array_merge($record, $updates);
                return $this->save();
            }
        }
        return false;
    }

    /**
     * Delete a record by ID.
     * Returns true on success, false if record not found.
     */
    public function delete(string $id): bool
    {
        foreach ($this->data as $index => $record) {
            if (isset($record['id']) && $record['id'] === $id) {
                array_splice($this->data, $index, 1);
                return $this->save();
            }
        }
        return false;
    }

    /**
     * Find records matching a condition.
     */
    public function where(string $field, mixed $value): array
    {
        return array_filter($this->data, function ($record) use ($field, $value) {
            return isset($record[$field]) && $record[$field] === $value;
        });
    }

    /**
     * Count all records.
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Generate a unique ID.
     */
    protected function generateId(): string
    {
        return uniqid('', true);
    }

    /**
     * Get the first record matching a condition.
     */
    public function findWhere(string $field, mixed $value): ?array
    {
        $results = $this->where($field, $value);
        return !empty($results) ? reset($results) : null;
    }

    /**
     * Check if a record exists by ID.
     */
    public function exists(string $id): bool
    {
        return $this->find($id) !== null;
    }
    /**
     * Generate a URL-safe slug from a string.
     */
    public static function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);

        return $text;
    }
}
