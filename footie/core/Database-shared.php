<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;

/**
 * Database connection manager (Singleton) - SHARED HOSTING VERSION.
 * This version supports connecting via Host/Port (standard MySQL) instead of Unix Socket.
 * 
 * Instructions:
 * 1. Upload this file to your server in the 'core/' directory.
 * 2. Rename it to 'Database.php' (replacing the existing file).
 */
class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::connect();
        }

        return self::$instance;
    }

    private static function connect(): void
    {
        $config = require dirname(__DIR__) . '/config/database.php';

        // Check if we have host configuration (Shared Hosting style)
        if (isset($config['host'])) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['port'] ?? 3306,
                $config['database']
            );
        }
        // Fallback to Socket (Galvani/Local style)
        elseif (isset($config['socket'])) {
            $dsn = sprintf(
                'mysql:unix_socket=%s;dbname=%s;charset=utf8mb4',
                $config['socket'],
                $config['database']
            );
        } else {
            throw new \RuntimeException('Database configuration invalid: Missing "host" or "socket"');
        }

        try {
            self::$instance = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
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

    public static function disconnect(): void
    {
        self::$instance = null;
    }
}
