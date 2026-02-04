<?php

declare(strict_types=1);

/**
 * Test runner for the Footie application.
 * Runs all test suites and outputs results.
 */

// Load bootstrap for autoloading
require_once __DIR__ . '/bootstrap.php';

use Tests\Core\DatabaseTest;
use Tests\Core\ModelTest;
use Tests\Models\SeasonTest;
use Tests\Models\TeamTest;
use Tests\Models\LeagueTest;
use Tests\Models\CupTest;

// Colors for console output
class Colors
{
    public const RESET = "\033[0m";
    public const RED = "\033[31m";
    public const GREEN = "\033[32m";
    public const YELLOW = "\033[33m";
    public const BLUE = "\033[34m";
    public const CYAN = "\033[36m";
}

class TestRunner
{
    private array $results = [];
    private int $totalTests = 0;
    private int $passedTests = 0;
    private int $failedTests = 0;
    private float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Run all test classes.
     */
    public function run(): void
    {
        echo Colors::CYAN . "╔════════════════════════════════════════════════════╗\n";
        echo "║         Footie CI Test Suite                      ║\n";
        echo "║         PDO Database Implementation               ║\n";
        echo "╚════════════════════════════════════════════════════╝\n" . Colors::RESET;

        $testClasses = [
            'Core: Database' => DatabaseTest::class,
            'Core: Model' => ModelTest::class,
            'Models: Seasons' => SeasonTest::class,
            'Models: Teams' => TeamTest::class,
            'Models: Leagues' => LeagueTest::class,
            'Models: Cups' => CupTest::class,
        ];

        foreach ($testClasses as $name => $class) {
            $this->runTestClass($name, $class);
        }

        $this->printSummary();
    }

    /**
     * Run a single test class.
     */
    private function runTestClass(string $name, string $className): void
    {
        echo "\n" . Colors::BLUE . "Testing {$name}...\n" . Colors::RESET;

        $testClass = new $className();

        // Get all test methods
        $reflection = new ReflectionClass($testClass);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        $classTests = 0;
        $classPassed = 0;

        foreach ($methods as $method) {
            if (str_starts_with($method->getName(), 'test')) {
                $classTests++;
                $this->totalTests++;

                try {
                    $testClass->runTest($method->getName());
                    echo Colors::GREEN . "  ✓ " . Colors::RESET . $method->getName() . "\n";
                    $classPassed++;
                    $this->passedTests++;
                } catch (Exception $e) {
                    echo Colors::RED . "  ✗ " . Colors::RESET . $method->getName() . "\n";
                    echo Colors::RED . "    Error: " . $e->getMessage() . "\n" . Colors::RESET;
                    $this->failedTests++;
                }
            }
        }

        // Clean up
        $testClass->teardown();

        // Print class summary
        $percentage = $classTests > 0 ? round(($classPassed / $classTests) * 100) : 0;
        echo Colors::CYAN . "  Summary: {$classPassed}/{$classTests} passed ({$percentage}%)\n" . Colors::RESET;
    }

    /**
     * Print final test summary.
     */
    private function printSummary(): void
    {
        $endTime = microtime(true);
        $duration = round($endTime - $this->startTime, 2);
        $percentage = $this->totalTests > 0 ? round(($this->passedTests / $this->totalTests) * 100) : 0;

        echo "\n" . Colors::CYAN . "╔════════════════════════════════════════════════════╗\n";
        echo "║ Test Results                                       ║\n";
        echo "╠════════════════════════════════════════════════════╣\n";

        $statusColor = $this->failedTests === 0 ? Colors::GREEN : Colors::RED;
        printf("║ Total Tests:    %3d                                   ║\n", $this->totalTests);
        printf("║ Passed:         %s%3d" . Colors::CYAN . "                                   ║\n", Colors::GREEN, $this->passedTests);
        printf("║ Failed:         %s%3d" . Colors::CYAN . "                                   ║\n", $this->failedTests > 0 ? Colors::RED : Colors::GREEN, $this->failedTests);
        printf("║ Success Rate:   %s%3d%%" . Colors::CYAN . "                                 ║\n", $statusColor, $percentage);
        printf("║ Duration:       %.2fs                                ║\n", $duration);

        echo "╚════════════════════════════════════════════════════╝\n" . Colors::RESET;

        if ($this->failedTests === 0) {
            echo Colors::GREEN . "\n✓ All tests passed!\n\n" . Colors::RESET;
            exit(0);
        } else {
            echo Colors::RED . "\n✗ Some tests failed.\n\n" . Colors::RESET;
            exit(1);
        }
    }
}

// Run tests
$runner = new TestRunner();
$runner->run();
