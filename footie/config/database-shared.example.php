<?php

declare(strict_types=1);

/**
 * Database configuration for Shared Hosting.
 * Rename this file to 'database.php' on your shared server.
 * 
 * CUSTOMER INSTRUCTION:
 * Replace the values below with your actual database credentials.
 * Because we are not using .env, you must hardcode them here.
 */
return [
    // Database Name
    'database' => 'YOUR_DATABASE_NAME_HERE',

    // Database Username
    'username' => 'YOUR_DATABASE_USERNAME_HERE',

    // Database Password
    'password' => 'YOUR_DATABASE_PASSWORD_HERE',

    // Database Host (usually 'localhost' on shared hosting)
    'host' => 'localhost',

    // Database Port (usually 3306)
    'port' => 3306,

    // PDO options
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
