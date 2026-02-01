<?php

declare(strict_types=1);

/**
 * Test bootstrap file.
 * Sets up autoloading for tests and application classes.
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Register autoloader
spl_autoload_register(function (string $class): void {
    // Map namespaces to directories
    $namespaces = [
        'Core\\' => 'core/',
        'App\\Controllers\\' => 'app/Controllers/',
        'App\\Models\\' => 'app/Models/',
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
