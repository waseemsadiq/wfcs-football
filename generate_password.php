<?php

declare(strict_types=1);

/**
 * Admin Password Generator
 * 
 * Generates a strong random password, hashes it, and updates the .env file.
 */

// Define base path
define('BASE_PATH', __DIR__);

// Function to generate random password
function generatePassword(int $length = 32): string
{
    return bin2hex(random_bytes($length / 2));
}

// Function to update or create .env file
function updateEnvFile(string $hash): void
{
    $envFile = BASE_PATH . '/.env';
    $envExample = BASE_PATH . '/.env.example';

    // Read existing .env or create from example
    if (file_exists($envFile)) {
        $content = file_get_contents($envFile);
    } elseif (file_exists($envExample)) {
        $content = file_get_contents($envExample);
        echo "Created .env file from .env.example\n";
    } else {
        $content = "";
        echo "Created new .env file\n";
    }

    // Check if ADMIN_PASSWORD_HASH exists
    if (preg_match('/^ADMIN_PASSWORD_HASH=.*$/m', $content)) {
        // Update existing line
        // Escape $ characters in hash because preg_replace treats them as backreferences
        $safeHash = str_replace('$', '\\$', $hash);
        $content = preg_replace(
            '/^ADMIN_PASSWORD_HASH=.*$/m',
            'ADMIN_PASSWORD_HASH=' . $safeHash,
            $content
        );
    } else {
        // Append if not exists
        $content .= "\n# Admin Password Hash\n";
        $content .= "ADMIN_PASSWORD_HASH=" . $hash . "\n";
    }

    // Write back to file
    if (file_put_contents($envFile, $content) !== false) {
        echo "Updated .env file with new password hash\n";
    } else {
        echo "Error: Failed to write to .env file\n";
        exit(1);
    }
}

// Main execution
echo "----------------------------------------\n";
echo "WFCS Football - Admin Password Generator\n";
echo "----------------------------------------\n\n";

// Generate password
$password = generatePassword(32);
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Generated Password: \033[32m" . $password . "\033[0m\n\n";
echo "IMPORTANT: Save this password now! You will need it to log in.\n";
echo "It will NOT be shown again.\n\n";

// Update .env
updateEnvFile($hash);

echo "Password hash stored in .env successfully.\n";
echo "----------------------------------------\n";
