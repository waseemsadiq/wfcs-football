<?php

declare(strict_types=1);

/**
 * Application bootstrap and entry point.
 * All requests are routed through this file.
 */

// Handle static files when using PHP built-in server
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $path;
    if (is_file($file)) {
        return false;
    }
}

// Start output buffering to prevent "headers already sent" issues
ob_start();

// Define base path
define('BASE_PATH', __DIR__);

// Register autoloader
spl_autoload_register(function (string $class): void {
    // Convert namespace to file path
    $prefix = '';
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
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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
$config = require BASE_PATH . '/config/app.php';

// Error handling based on debug mode
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    // Force a specific session name to avoid conflicts with other local apps
    session_name('FOOTIE_SESSION');

    // Security settings for session cookies
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Strict');

    // Enable secure flag for HTTPS connections
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }

    // Session timeout: 2 hours of inactivity
    ini_set('session.gc_maxlifetime', '7200');
    ini_set('session.cookie_lifetime', '7200');

    session_start();

    // Check for session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
        // Session expired, destroy it
        session_unset();
        session_destroy();
        session_start();
    }

    $_SESSION['last_activity'] = time();
}

// Load routes and dispatch request
$router = require BASE_PATH . '/config/routes.php';

// Set debug mode on router for error page display
$router->setDebug($config['debug']);

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);
