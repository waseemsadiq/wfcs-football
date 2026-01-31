<?php

declare(strict_types=1);

namespace Core;

/**
 * Simple password-based authentication.
 * Uses PHP sessions to maintain login state.
 */
class Auth
{
    private const SESSION_KEY = 'authenticated';

    /**
     * Start session if not already started.
     * Note: Most sessions are started in index.php, but this is a safety fallback.
     */
    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name('FOOTIE_SESSION');

            if (!session_start()) {
                throw new \RuntimeException('Failed to start session');
            }
        }
    }

    /**
     * Attempt to log in with the given password.
     * Returns true if password is correct.
     * Implements rate limiting to prevent brute force attacks.
     */
    public static function attempt(string $password): bool
    {
        self::ensureSession();

        // Check if account is locked due to too many failed attempts
        if (self::isLoginBlocked()) {
            return false;
        }

        $passwordHash = self::getConfigPasswordHash();

        if (password_verify($password, $passwordHash)) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            $_SESSION[self::SESSION_KEY] = true;
            $_SESSION['login_time'] = time();

            // Clear failed login attempts on success
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_block_until']);

            return true;
        }

        // Record failed login attempt
        self::recordFailedLogin();

        return false;
    }

    /**
     * Check if the user is authenticated.
     */
    public static function check(): bool
    {
        self::ensureSession();
        return isset($_SESSION[self::SESSION_KEY]) && $_SESSION[self::SESSION_KEY] === true;
    }

    /**
     * Log out the current user.
     */
    public static function logout(): void
    {
        self::ensureSession();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Get the configured admin password hash.
     */
    private static function getConfigPasswordHash(): string
    {
        $config = require dirname(__DIR__) . '/config/app.php';
        return $config['admin_password_hash'] ?? '';
    }

    /**
     * Get the time the user logged in.
     */
    public static function loginTime(): ?int
    {
        self::ensureSession();
        return $_SESSION['login_time'] ?? null;
    }

    /**
     * Generate a CSRF token and store in session.
     */
    public static function generateCsrfToken(): string
    {
        self::ensureSession();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validate the CSRF token from a form submission.
     */
    public static function validateCsrfToken(?string $token): bool
    {
        self::ensureSession();

        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get the current CSRF token (or generate if needed).
     */
    public static function csrfToken(): string
    {
        return self::generateCsrfToken();
    }

    /**
     * Check if login attempts are currently blocked.
     * Blocks after 5 failed attempts for 15 minutes.
     */
    private static function isLoginBlocked(): bool
    {
        self::ensureSession();

        if (isset($_SESSION['login_block_until'])) {
            if (time() < $_SESSION['login_block_until']) {
                return true;
            }

            // Block period expired, clear it
            unset($_SESSION['login_block_until']);
            unset($_SESSION['login_attempts']);
        }

        return false;
    }

    /**
     * Record a failed login attempt.
     * After 5 failed attempts, block login for 15 minutes.
     */
    private static function recordFailedLogin(): void
    {
        self::ensureSession();

        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['first_attempt_time'] = time();
        }

        $_SESSION['login_attempts']++;

        // If 5 or more failed attempts, block for 15 minutes
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_block_until'] = time() + 900; // 15 minutes
        }
    }

    /**
     * Get the time remaining until login is unblocked.
     * Returns null if not blocked, otherwise seconds remaining.
     */
    public static function getBlockTimeRemaining(): ?int
    {
        self::ensureSession();

        if (isset($_SESSION['login_block_until'])) {
            $remaining = $_SESSION['login_block_until'] - time();
            return $remaining > 0 ? $remaining : null;
        }

        return null;
    }

    /**
     * Get the number of failed login attempts.
     */
    public static function getFailedAttempts(): int
    {
        self::ensureSession();
        return $_SESSION['login_attempts'] ?? 0;
    }
}
