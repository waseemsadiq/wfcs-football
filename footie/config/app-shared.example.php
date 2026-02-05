<?php

declare(strict_types=1);

/**
 * Application configuration for Shared Hosting.
 * Rename this file to 'app.php' on your shared server.
 * 
 * CUSTOMER INSTRUCTION:
 * Replace the values below with your actual settings.
 */
return [
    // Application name shown in the browser title and header
    'name' => 'WFCS Football',

    // Admin password hash for authentication.
    // YOU MUST GENERATE A HASH.
    // To generate a hash, you can use an online bcrypt generator or run `php -r "echo password_hash('your_password', PASSWORD_BCRYPT);"` in your terminal.
    // Example hash for "admin": $2y$12$7J... (this is just an example, do not use)
    'admin_password_hash' => 'YOUR_BCRYPT_PASSWORD_HASH_HERE',

    // Base path for data files
    'data_path' => dirname(__DIR__) . '/data',

    // Enable debug mode (shows detailed errors)
    // Set to true only for troubleshooting, false for production.
    'debug' => false,
];
