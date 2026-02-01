<?php

declare(strict_types=1);

namespace Tests;

use Exception;

/**
 * Base test case class for all tests.
 * Provides common setup, teardown, and assertion helpers.
 */
abstract class TestCase
{
    protected string $testDataPath;
    protected array $testFailures = [];
    protected array $testResults = [];

    public function __construct()
    {
        $this->testDataPath = sys_get_temp_dir() . '/footie-tests-' . uniqid();
        $this->setup();
    }

    /**
     * Set up test environment.
     */
    protected function setup(): void
    {
        if (!is_dir($this->testDataPath)) {
            mkdir($this->testDataPath, 0755, true);
        }
    }

    /**
     * Clean up test files after tests complete.
     */
    public function teardown(): void
    {
        $this->removeDirectory($this->testDataPath);
    }

    /**
     * Remove a directory and all its contents.
     */
    protected function removeDirectory(string $path): void
    {
        if (is_dir($path)) {
            $items = scandir($path);
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..') {
                    $itemPath = $path . '/' . $item;
                    if (is_dir($itemPath)) {
                        $this->removeDirectory($itemPath);
                    } else {
                        unlink($itemPath);
                    }
                }
            }
            rmdir($path);
        }
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
     * Create a test data directory.
     */
    protected function createTestDataDir(string $subdir = ''): string
    {
        $dir = $this->testDataPath . '/' . $subdir;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }
}
