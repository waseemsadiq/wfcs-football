<?php

declare(strict_types=1);

/**
 * Database configuration for MySQL connection.
 */
return [
    // Database connection details
    'database' => getenv('DB_NAME') ?: '',
    'username' => getenv('DB_USER') ?: '',
    'password' => getenv('DB_PASS') ?: '',
    'host' => getenv('DB_HOST') ?: '',
    'port' => getenv('DB_PORT') ?: 3306,

    // Unix socket path for Galvani's embedded MySQL
    // Goes up 1 level from app root to reach the distribution root where data/ is located
    'socket' => dirname(__DIR__) . '/../data/mysql.sock',

    // PDO options
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => true,  // Required for correct DATE/TIME handling with MySQL socket
    ],
];
