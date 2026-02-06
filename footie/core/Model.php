<?php

declare(strict_types=1);

namespace Core;

use PDO;

/**
 * Base model for MySQL database-backed entities.
 * Provides CRUD operations using PDO.
 */
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get the database table name for this model.
     * Child classes must implement this.
     */
    abstract protected function getTableName(): string;

    /**
     * Transform snake_case database column names to camelCase keys.
     * Used for OUTPUT (database → PHP).
     */
    protected function transformKeys(array $record): array
    {
        $transformed = [];
        foreach ($record as $key => $value) {
            // Convert snake_case to camelCase
            $camelKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
            $transformed[$camelKey] = $value;
        }
        return $transformed;
    }



    /**
     * Transform array of records.
     */
    protected function transformRows(array $records): array
    {
        return array_map(fn($record) => $this->transformKeys($record), $records);
    }

    /**
     * Get all records from the table.
     */
    public function all(): array
    {
        $table = $this->getTableName();
        $stmt = $this->db->query("SELECT * FROM `{$table}`");

        return $this->transformRows($stmt->fetchAll());
    }

    /**
     * Find a record by ID.
     */
    public function find(int|string $id): ?array
    {
        $table = $this->getTableName();
        $stmt = $this->db->prepare("SELECT * FROM `{$table}` WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);

        $result = $stmt->fetch();
        return $result ? $this->transformKeys($result) : null;
    }

    /**
     * Create a new record.
     * Returns the created record with generated ID.
     */
    public function create(array $record): array
    {
        $table = $this->getTableName();



        // Add timestamps
        $record['created_at'] = date('Y-m-d H:i:s');
        $record['updated_at'] = date('Y-m-d H:i:s');

        // Build INSERT query
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

        // Get the inserted record (will be transformed back to camelCase by find())
        $id = $this->db->lastInsertId();
        return $this->find($id);
    }

    /**
     * Update an existing record.
     * Returns true on success, false if record not found.
     */
    public function update(int|string $id, array $updates): bool
    {
        $table = $this->getTableName();

        // Check if record exists
        if (!$this->find($id)) {
            return false;
        }



        // Add updated timestamp
        $updates['updated_at'] = date('Y-m-d H:i:s');

        // Build UPDATE query
        $fields = array_keys($updates);
        $setClause = implode(', ', array_map(fn($f) => "`{$f}` = ?", $fields));

        $sql = "UPDATE `{$table}` SET {$setClause} WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $values = array_values($updates);
        $values[] = $id;

        return $stmt->execute($values);
    }

    /**
     * Delete a record by ID.
     * Returns true on success, false if record not found.
     */
    public function delete(int|string $id): bool
    {
        $table = $this->getTableName();

        // Check if record exists
        if (!$this->find($id)) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM `{$table}` WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Find records matching a condition.
     */
    public function where(string $field, mixed $value): array
    {
        $table = $this->getTableName();
        $stmt = $this->db->prepare("SELECT * FROM `{$table}` WHERE `{$field}` = ?");
        $stmt->execute([$value]);

        return $this->transformRows($stmt->fetchAll());
    }

    /**
     * Count records with optional WHERE conditions.
     */
    public function count(array $where = []): int
    {
        $table = $this->getTableName();
        $sql = "SELECT COUNT(*) FROM `{$table}`";

        $params = [];
        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                if (is_array($value) && count($value) === 2) {
                    $operator = $value[0];
                    $val = $value[1];
                    $conditions[] = "`{$column}` {$operator} ?";
                    $params[] = $val;
                } else {
                    $conditions[] = "`{$column}` = ?";
                    $params[] = $value;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if (empty($params)) {
            $stmt = $this->db->query($sql);
            return (int) $stmt->fetchColumn();
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        }
    }

    /**
     * Get the first record matching a condition.
     * Field name can be in camelCase or snake_case.
     */
    public function findWhere(string $field, mixed $value): ?array
    {
        $table = $this->getTableName();

        // Transform field name to snake_case for database
        $dbField = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $field));

        $stmt = $this->db->prepare("SELECT * FROM `{$table}` WHERE `{$dbField}` = ? LIMIT 1");
        $stmt->execute([$value]);

        $result = $stmt->fetch();
        return $result ? $this->transformKeys($result) : null;
    }

    /**
     * Check if a record exists by ID.
     */
    public function exists(int|string $id): bool
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

    /**
     * Generate a unique slug by appending a number if needed.
     * Checks the database to ensure the slug doesn't already exist.
     *
     * @param string $baseText The text to slugify
     * @param int|string|null $excludeId Optional ID to exclude from uniqueness check (for updates)
     * @return string A guaranteed unique slug
     */
    public function generateUniqueSlug(string $baseText, int|string|null $excludeId = null): string
    {
        $baseSlug = self::slugify($baseText);
        $slug = $baseSlug;
        $counter = 1;
        $table = $this->getTableName();

        // Keep trying until we find a unique slug
        while (true) {
            $sql = "SELECT COUNT(*) FROM `{$table}` WHERE slug = ?";
            $params = [$slug];

            // Exclude current record if updating
            if ($excludeId !== null) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $count = (int) $stmt->fetchColumn();

            if ($count === 0) {
                return $slug; // Found a unique slug
            }

            // Slug exists, try with a number suffix
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
    }

    /**
     * Get paginated results with optional filtering.
     *
     * @param int $limit Number of records per page
     * @param int $offset Starting offset
     * @param array $where Optional where conditions ['column' => 'value']
     * @param string $orderBy Optional order by column
     * @param string $orderDir Order direction (ASC or DESC)
     * @return array Array of records
     */
    public function paginate(int $limit = 20, int $offset = 0, array $where = [], string $orderBy = 'id', string $orderDir = 'DESC'): array
    {
        $table = $this->getTableName();
        $sql = "SELECT * FROM {$table}";

        $params = [];
        if (!empty($where)) {
            $conditions = [];
            $i = 0;
            foreach ($where as $column => $value) {
                $paramName = "where_{$i}";
                if (is_array($value) && count($value) === 2) {
                    $operator = $value[0];
                    $val = $value[1];
                    $conditions[] = "`{$column}` {$operator} :{$paramName}";
                    $params[$paramName] = $val;
                } else {
                    $conditions[] = "`{$column}` = :{$paramName}";
                    $params[$paramName] = $value;
                }
                $i++;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY {$orderBy} {$orderDir}";
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        // Bind WHERE parameters
        foreach ($params as $name => $value) {
            $stmt->bindValue(":{$name}", $value);
        }

        // Bind LIMIT and OFFSET as integers
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $stmt->execute();

        return $this->transformRows($stmt->fetchAll());
    }

}
