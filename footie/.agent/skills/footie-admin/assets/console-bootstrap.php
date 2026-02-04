<?php

declare(strict_types=1);

/**
 * Console bootstrap for footie-admin skill.
 * Usage: require 'console-bootstrap.php';
 * 
 * This file is intended to be copied to the application root temporarily
 * to allow admin scripts to access the application Models and Database.
 */

// Define base path as current directory (since this file is copied to app root)
define('BASE_PATH', __DIR__);

// Register autoloader
spl_autoload_register(function (string $class): void {
    // Convert namespace to file path
    $baseDir = BASE_PATH . '/';

    // Map namespaces to directories
    $namespaces = [
        'Core\\' => 'core/',
        'App\\Controllers\\' => 'app/Controllers/',
        'App\\Models\\' => 'app/Models/',
    ];

    foreach ($namespaces as $namespace => $dir) {
        if (str_starts_with($class, $namespace)) {
            $relativeClass = substr($class, strlen($namespace));
            $file = $baseDir . $dir . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

// Manual .env loader for environment variables
$envPath = BASE_PATH . '/.env';
if (!file_exists($envPath)) {
    // Try parent directory
    $envPath = dirname(BASE_PATH) . '/.env';
}

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        if (str_contains($line, '=')) {
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!empty($name) && !getenv($name)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Load configuration
if (file_exists(BASE_PATH . '/config/app.php')) {
    $config = require BASE_PATH . '/config/app.php';

    // Error handling based on debug mode
    if (isset($config['debug']) && $config['debug']) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    } else {
        error_reporting(0);
        ini_set('display_errors', '0');
    }
}
