<?php

declare(strict_types=1);

/**
 * Application configuration.
 */
return [
    // Application name shown in the browser title and header
    'name' => 'WFCS Football',

    // Admin password hash for authentication
    // Generate a new hash with: php -r "echo password_hash('yourpassword', PASSWORD_DEFAULT);"
    'admin_password_hash' => '$2y$12$bWGKJkLVyEl44Q0ITFlDKO.YJaCap6DtkusfSK05qMbmDXXKPKDw2',

    // Base path for data files
    'data_path' => dirname(__DIR__) . '/data',

    // Enable debug mode (shows detailed errors)
    // IMPORTANT: Set to false in production!
    // Can be controlled via environment variable: export FOOTIE_DEBUG=false
    'debug' => filter_var(
        getenv('FOOTIE_DEBUG') !== false ? getenv('FOOTIE_DEBUG') : 'true',
        FILTER_VALIDATE_BOOLEAN
    ),
];
