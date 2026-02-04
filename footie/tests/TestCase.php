<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use PDO;
use Core\Database;

/**
 * Base test case class for all tests.
 * Provides common setup, teardown, and assertion helpers.
 *
 * Manages a test database with automatic cleanup between tests.
 */
abstract class TestCase
{
    protected PDO $db;
    protected array $testFailures = [];
    protected array $createdIds = [
        'teams' => [],
        'seasons' => [],
        'leagues' => [],
        'cups' => [],
    ];

    public function __construct()
    {
        $this->setup();
    }

    /**
     * Set up test environment.
     * Initializes database connection and cleans tables.
     */
    protected function setup(): void
    {
        $this->db = Database::getInstance();
        $this->cleanDatabase();
    }

    /**
     * Clean the database for fresh test state.
     * Deletes all records from test tables.
     */
    protected function cleanDatabase(): void
    {
        // Disable foreign key checks temporarily
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 0');

        // Truncate all tables in reverse dependency order
        $tables = [
            'cup_fixtures',
            'cup_rounds',
            'cup_teams',
            'league_fixtures',
            'league_teams',
            'players',
            'cups',
            'leagues',
            'teams',
            'seasons',
        ];

        foreach ($tables as $table) {
            $this->db->exec("TRUNCATE TABLE `{$table}`");
        }

        // Re-enable foreign key checks
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Clean up test data after tests complete.
     */
    public function teardown(): void
    {
        $this->cleanDatabase();
    }

    /**
     * Assert that a value is true.
     */
    protected function assertTrue(mixed $value, string $message = ''): void
    {
        if ($value !== true) {
            $this->fail("Expected true but got " . var_export($value, true) . ". {$message}");
        }
    }

    /**
     * Assert that a value is false.
     */
    protected function assertFalse(mixed $value, string $message = ''): void
    {
        if ($value !== false) {
            $this->fail("Expected false but got " . var_export($value, true) . ". {$message}");
        }
    }

    /**
     * Assert that two values are equal.
     */
    protected function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            $this->fail("Expected " . var_export($expected, true) . " but got " . var_export($actual, true) . ". {$message}");
        }
    }

    /**
     * Assert that a value is null.
     */
    protected function assertNull(mixed $value, string $message = ''): void
    {
        if ($value !== null) {
            $this->fail("Expected null but got " . var_export($value, true) . ". {$message}");
        }
    }

    /**
     * Assert that a value is not null.
     */
    protected function assertNotNull(mixed $value, string $message = ''): void
    {
        if ($value === null) {
            $this->fail("Expected not null. {$message}");
        }
    }

    /**
     * Assert that an array has a key.
     */
    protected function assertArrayHasKey(string $key, array $array, string $message = ''): void
    {
        if (!array_key_exists($key, $array)) {
            $this->fail("Array does not have key '{$key}'. {$message}");
        }
    }

    /**
     * Assert that an array does not have a key.
     */
    protected function assertArrayNotHasKey(string $key, array $array, string $message = ''): void
    {
        if (array_key_exists($key, $array)) {
            $this->fail("Array has key '{$key}' but shouldn't. {$message}");
        }
    }

    /**
     * Assert that a count matches.
     */
    protected function assertCount(int $expected, array|object $actual, string $message = ''): void
    {
        $count = is_array($actual) ? count($actual) : (property_exists($actual, 'count') ? $actual->count() : 0);
        if ($count !== $expected) {
            $this->fail("Expected count {$expected} but got {$count}. {$message}");
        }
    }

    /**
     * Assert that a string contains a substring.
     */
    protected function assertStringContains(string $needle, string $haystack, string $message = ''): void
    {
        if (!str_contains($haystack, $needle)) {
            $this->fail("String does not contain '{$needle}'. {$message}");
        }
    }

    /**
     * Assert that a value is not empty.
     */
    protected function assertNotEmpty(mixed $value, string $message = ''): void
    {
        if (empty($value)) {
            $this->fail("Expected non-empty but got empty. {$message}");
        }
    }

    /**
     * Assert that two values are not equal.
     */
    protected function assertNotEqual(mixed $unexpected, mixed $actual, string $message = ''): void
    {
        if ($unexpected === $actual) {
            $this->fail("Expected not " . var_export($unexpected, true) . " but got " . var_export($actual, true) . ". {$message}");
        }
    }

    /**
     * Assert that a value is greater than another.
     */
    protected function assertGreaterThan(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($actual <= $expected) {
            $this->fail("Expected {$actual} to be greater than {$expected}. {$message}");
        }
    }

    /**
     * Assert that a value is less than another.
     */
    protected function assertLessThan(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($actual >= $expected) {
            $this->fail("Expected {$actual} to be less than {$expected}. {$message}");
        }
    }

    /**
     * Assert that an array is empty.
     */
    protected function assertEmpty(mixed $value, string $message = ''): void
    {
        if (!empty($value)) {
            $this->fail("Expected empty but got non-empty. {$message}");
        }
    }

    /**
     * Assert that two arrays have the same values (order-independent).
     */
    protected function assertArrayEquals(array $expected, array $actual, string $message = ''): void
    {
        sort($expected);
        sort($actual);
        if ($expected !== $actual) {
            $this->fail("Arrays do not match. {$message}");
        }
    }

    /**
     * Assert that a value is an instance of a class.
     */
    protected function assertInstanceOf(string $expected, mixed $actual, string $message = ''): void
    {
        if (!($actual instanceof $expected)) {
            $actualType = is_object($actual) ? get_class($actual) : gettype($actual);
            $this->fail("Expected instance of {$expected} but got {$actualType}. {$message}");
        }
    }

    /**
     * Assert that two values are the same (identical).
     */
    protected function assertSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            $this->fail("Expected same instance. {$message}");
        }
    }

    /**
     * Assert that a value contains a value (in_array).
     */
    protected function assertContains(mixed $needle, array $haystack, string $message = ''): void
    {
        if (!in_array($needle, $haystack, true)) {
            $this->fail("Array does not contain " . var_export($needle, true) . ". {$message}");
        }
    }

    /**
     * Assert that a value is an array.
     */
    protected function assertIsArray(mixed $value, string $message = ''): void
    {
        if (!is_array($value)) {
            $this->fail("Expected array but got " . gettype($value) . ". {$message}");
        }
    }

    /**
     * Fail a test with a message.
     */
    protected function fail(string $message = ''): void
    {
        throw new Exception("Test failed: {$message}");
    }

    /**
     * Run a single test method.
     */
    public function runTest(string $testMethod): bool
    {
        try {
            $this->$testMethod();
            return true;
        } catch (Exception $e) {
            $this->testFailures[$testMethod] = $e->getMessage();
            return false;
        }
    }

    /**
     * Get test failures.
     */
    public function getFailures(): array
    {
        return $this->testFailures;
    }

    /**
     * Create test teams for fixtures.
     */
    protected function createTestTeams(int $count = 4): array
    {
        $teams = [];
        $teamNames = ['Arsenal', 'Chelsea', 'Liverpool', 'Manchester United', 'Tottenham', 'Manchester City', 'Everton', 'Newcastle'];

        for ($i = 0; $i < $count; $i++) {
            $name = $teamNames[$i] ?? "Team " . ($i + 1);
            $colourValue = mt_rand(0, 0xFFFFFF);
            $colour = sprintf('#%06X', $colourValue);
            $slug = strtolower(str_replace(' ', '-', $name));

            $stmt = $this->db->prepare("
                INSERT INTO teams (name, slug, colour, created_at, updated_at)
                VALUES (?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([$name, $slug, $colour]);
            $teams[] = (int) $this->db->lastInsertId();
        }

        return $teams;
    }

    /**
     * Create a test season.
     */
    protected function createTestSeason(string $id = '2025-26', bool $active = true): int|string
    {
        $seasonName = $id . ' Season';
        $slug = strtolower(str_replace(' ', '-', $seasonName));

        $stmt = $this->db->prepare("
            INSERT INTO seasons (id, name, slug, start_date, end_date, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $id,
            $seasonName,
            $slug,
            '2025-08-01',
            '2026-05-31',
            $active ? 1 : 0
        ]);
        return $id;
    }
}
