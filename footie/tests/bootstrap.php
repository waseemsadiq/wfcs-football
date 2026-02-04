<?php

declare(strict_types=1);

/**
 * Test bootstrap file.
 * Sets up autoloading for tests and application classes.
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load environment variables
if (file_exists(BASE_PATH . '/../.env')) {
    $lines = file(BASE_PATH . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Register autoloader
spl_autoload_register(function (string $class): void {
    // Map namespaces to directories
    $namespaces = [
        'Core\\' => 'core/',
        'App\\Controllers\\' => 'app/Controllers/',
        'App\\Models\\' => 'app/Models/',
        'App\\Models\\Traits\\' => 'app/Models/Traits/',
        'Tests\\' => 'tests/',
    ];

    foreach ($namespaces as $namespace => $dir) {
        if (str_starts_with($class, $namespace)) {
            $baseDir = BASE_PATH . '/' . $dir;
            $relative = substr($class, strlen($namespace));
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});
