<?php

declare(strict_types=1);

/**
 * Application configuration.
 */
return [
    // Application name shown in the browser title and header
    'name' => 'WFCS Football',

    // Admin password hash for authentication
    // Loaded from .env file or environment variable
    'admin_password_hash' => getenv('ADMIN_PASSWORD_HASH') ?: '',

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
