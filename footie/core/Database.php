<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;

/**
 * Database connection manager (Singleton).
 * Provides a PDO connection to the MySQL database.
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Private constructor to enforce singleton pattern.
     */
    private function __construct()
    {
    }

    /**
     * Get the PDO instance.
     * Creates the connection on first call, reuses it afterwards.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::connect();
        }

        return self::$instance;
    }

    /**
     * Create the database connection.
     */
    private static function connect(): void
    {
        $config = require dirname(__DIR__) . '/config/database.php';

        $dsn = sprintf(
            'mysql:unix_socket=%s;dbname=%s;charset=utf8mb4',
            $config['socket'],
            $config['database']
        );

        try {
            self::$instance = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            // In production, log error and show generic message
            // In development, show detailed error
            $appConfig = require dirname(__DIR__) . '/config/app.php';

            if ($appConfig['debug']) {
                throw new \RuntimeException(
                    "Database connection failed: {$e->getMessage()}"
                );
            } else {
                throw new \RuntimeException('Database connection failed');
            }
        }
    }

    /**
     * Close the database connection.
     * Useful for testing or explicit cleanup.
     */
    public static function disconnect(): void
    {
        self::$instance = null;
    }
}
