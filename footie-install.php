<?php
/**
 * Footie App Installer for Galvani
 *
 * Creates MySQL database and schema for WFCS Football app.
 *
 * Usage:
 *   ./galvani footie-install.php [options]
 *
 * Options:
 *   --help                Show this help message
 *   --app-dir=DIR         Directory where app is installed (default: "footie")
 *   --db-name=NAME        Database name (default: "wfcs")
 *   --db-user=USER        Database user (default: "root")
 *   --db-pass=PASS        Database password (default: "")
 *   --load-sample=Y/n     Load sample data (default: Y)
 *   --admin-password=PASS Admin password (default: "admin")
 */

// ============================================================================
// Helper Functions
// ============================================================================

/**
 * Get option value from CLI flags or interactive prompt
 */
function getOption(array $options, string $key, string $prompt, string $default = ''): string
{
    if (isset($options[$key])) {
        return $options[$key];
    }
    echo "$prompt [$default]: ";
    $input = trim(fgets(STDIN));
    return empty($input) ? $default : $input;
}

// ============================================================================
// Command-line arguments
// ============================================================================

$options = getopt('hd:', [
    'help',
    'app-dir:',
    'db-name:',
    'db-user:',
    'db-pass:',
    'load-sample:',
    'admin-password:'
]);

if (isset($options['h']) || isset($options['help'])) {
    echo <<<HELP
Footie App Installer for Galvani

Creates MySQL database and schema for WFCS Football app.

Usage:
  ./galvani footie-install.php [options]

Options:
  -h, --help                   Show this help message
  -d, --app-dir DIR            Directory where app is installed (default: "footie")
  --db-name NAME               Database name (default: "wfcs")
  --db-user USER               Database user (default: "root")
  --db-pass PASS               Database password (default: "")
  --load-sample Y/n            Load sample data (default: Y)
  --admin-password PASS        Admin password (default: "admin")

Note: Run from the galvani distribution root.
      Requires MySQL enabled (GALVANI_MYSQL=1 in .env).

Examples:
  ./galvani footie-install.php
  ./galvani footie-install.php --app-dir=sports --db-name=my_league
  ./galvani footie-install.php --db-user=admin --db-pass=secret
  ./galvani footie-install.php --admin-password=SecurePass123 --load-sample=n

HELP;
    exit(0);
}

// Collect installation settings (CLI flags or interactive prompts)
echo "\n";
echo "==========================================\n";
echo " Footie App Installer\n";
echo "==========================================\n";
echo "\n";

$appDir = getOption($options, 'app-dir', 'Where should the Footie app be installed?', 'footie');
$appDir = rtrim($appDir, '/');

$dbName = getOption($options, 'db-name', 'Database name', 'wfcs');
$dbUser = getOption($options, 'db-user', 'Database username', 'root');
$dbPass = getOption($options, 'db-pass', 'Database password', '');

$loadSample = getOption($options, 'load-sample', 'Load sample data (teams, fixtures, seasons)? (Y/n)', 'Y');
$adminPassword = getOption($options, 'admin-password', "Set admin password (leave blank for default 'admin')", '');

echo "\n";

// ============================================================================
// Configuration
// ============================================================================

$config = [
    'db_name' => $dbName,
    'db_user' => $dbUser,
    'db_pass' => $dbPass,
    'socket_path' => getcwd() . '/data/mysql.sock',
];

// ============================================================================
// Database Schema
// ============================================================================

$tables = [
    'seasons' => "CREATE TABLE seasons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        is_active BOOLEAN DEFAULT FALSE,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'teams' => "CREATE TABLE teams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        contact VARCHAR(100),
        phone VARCHAR(50),
        email VARCHAR(100),
        colour VARCHAR(7) DEFAULT '#000000',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'players' => "CREATE TABLE players (
        id INT AUTO_INCREMENT PRIMARY KEY,
        team_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'leagues' => "CREATE TABLE leagues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        season_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        start_date DATE,
        frequency ENUM('weekly', 'fortnightly', 'monthly') DEFAULT 'weekly',
        match_time TIME DEFAULT '15:00:00',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'league_teams' => "CREATE TABLE league_teams (
        league_id INT NOT NULL,
        team_id INT NOT NULL,
        PRIMARY KEY (league_id, team_id),
        FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE,
        FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'league_fixtures' => "CREATE TABLE league_fixtures (
        id INT AUTO_INCREMENT PRIMARY KEY,
        league_id INT NOT NULL,
        home_team_id INT NOT NULL,
        away_team_id INT NOT NULL,
        match_date DATE,
        match_time TIME,
        home_score INT,
        away_score INT,
        home_scorers TEXT,
        away_scorers TEXT,
        home_cards TEXT,
        away_cards TEXT,
        created_at DATETIME NOT NULL,
        FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE,
        FOREIGN KEY (home_team_id) REFERENCES teams(id),
        FOREIGN KEY (away_team_id) REFERENCES teams(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'cups' => "CREATE TABLE cups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        season_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        start_date DATE,
        frequency ENUM('weekly', 'fortnightly', 'monthly') DEFAULT 'weekly',
        match_time TIME DEFAULT '15:00:00',
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'cup_teams' => "CREATE TABLE cup_teams (
        cup_id INT NOT NULL,
        team_id INT NOT NULL,
        PRIMARY KEY (cup_id, team_id),
        FOREIGN KEY (cup_id) REFERENCES cups(id) ON DELETE CASCADE,
        FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'cup_rounds' => "CREATE TABLE cup_rounds (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cup_id INT NOT NULL,
        name VARCHAR(50) NOT NULL,
        round_order INT NOT NULL,
        FOREIGN KEY (cup_id) REFERENCES cups(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

    'cup_fixtures' => "CREATE TABLE cup_fixtures (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cup_id INT NOT NULL,
        round_id INT NOT NULL,
        home_team_id INT,
        away_team_id INT,
        match_date DATE,
        match_time TIME,
        home_score INT,
        away_score INT,
        home_scorers TEXT,
        away_scorers TEXT,
        home_cards TEXT,
        away_cards TEXT,
        extra_time BOOLEAN DEFAULT FALSE,
        home_score_et INT,
        away_score_et INT,
        penalties BOOLEAN DEFAULT FALSE,
        home_pens INT,
        away_pens INT,
        winner ENUM('home', 'away'),
        created_at DATETIME NOT NULL,
        FOREIGN KEY (cup_id) REFERENCES cups(id) ON DELETE CASCADE,
        FOREIGN KEY (round_id) REFERENCES cup_rounds(id) ON DELETE CASCADE,
        FOREIGN KEY (home_team_id) REFERENCES teams(id),
        FOREIGN KEY (away_team_id) REFERENCES teams(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
];

// ============================================================================
// Preflight checks
// ============================================================================

echo "Checking prerequisites...\n";

// Handle directory renaming if needed
if ($appDir !== 'footie') {
    if (file_exists($appDir)) {
        echo "Error: Directory '$appDir' already exists\n";
        exit(1);
    }

    if (!file_exists(__DIR__ . '/footie')) {
        echo "Error: Source directory 'footie' not found\n";
        exit(1);
    }

    echo "Moving footie/ to {$appDir}/...\n";
    if (!rename(__DIR__ . '/footie', __DIR__ . '/' . $appDir)) {
        echo "Error: Failed to rename directory\n";
        exit(1);
    }


    echo "  Moved successfully\n";
}

if (!is_dir($appDir)) {
    echo "Error: App directory '$appDir' not found.\n";
    exit(1);
}

// Check socket exists (MySQL should be running)
if (!file_exists($config['socket_path'])) {
    echo "Error: MySQL socket not found at {$config['socket_path']}\n";
    echo "Make sure MySQL is enabled (GALVANI_MYSQL=1 in .env or --mysql flag).\n";
    exit(1);
}

echo "MySQL socket found\n";

// Check mysqli extension
if (!extension_loaded('mysqli')) {
    echo "Error: mysqli extension not loaded\n";
    exit(1);
}

// ============================================================================
// Connect to database
// ============================================================================

echo "Connecting to MySQL...\n";

$mysqli = @new mysqli('localhost', $config['db_user'], $config['db_pass'], '', 0, $config['socket_path']);

if ($mysqli->connect_error) {
    echo "Error: Cannot connect to MySQL: {$mysqli->connect_error}\n";
    exit(1);
}

echo "Connected to MySQL\n";

// ============================================================================
// Create database
// ============================================================================

echo "Creating database '{$config['db_name']}'...\n";

$mysqli->query("CREATE DATABASE IF NOT EXISTS `{$config['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

if ($mysqli->error) {
    echo "Error: Failed to create database: {$mysqli->error}\n";
    exit(1);
}

$mysqli->select_db($config['db_name']);

echo "Database ready\n\n";

// ============================================================================
// Create tables
// ============================================================================

echo "Creating tables...\n";

$created = [];
$skipped = [];

foreach ($tables as $name => $sql) {
    // Check if table exists
    $result = $mysqli->query("SHOW TABLES LIKE '$name'");

    if ($result && $result->num_rows > 0) {
        $skipped[] = $name;
        echo "  Table '$name' already exists, skipping\n";
        continue;
    }

    // Create table
    if ($mysqli->query($sql)) {
        $created[] = $name;
        echo "  Created table '$name'\n";
    } else {
        echo "Error: Failed to create table '$name': {$mysqli->error}\n";
        exit(1);
    }
}

// ============================================================================
// Load sample data
// ============================================================================

if (strtolower($loadSample) !== 'n') {
    echo "\nLoading sample data...\n";

    $sqlFile = __DIR__ . '/sample-content.sql';
    if (!file_exists($sqlFile)) {
        echo "  Warning: sample-content.sql not found, skipping\n";
    } else {
        // Use mysql command to load SQL file (handles MariaDB dumps better than multi_query)
        // All values are escaped with escapeshellarg() for security
        $command = sprintf(
            'mysql -u %s --socket=%s --skip-ssl %s < %s 2>&1',
            escapeshellarg($config['db_user']),
            escapeshellarg($config['socket_path']),
            escapeshellarg($config['db_name']),
            escapeshellarg($sqlFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            echo "  Warning: Error loading sample data:\n";
            echo "    " . implode("\n    ", $output) . "\n";
        } else {
            echo "  Sample data loaded successfully\n";
        }
    }
}

// Close connection
$mysqli->close();

// ============================================================================
// Summary
// ============================================================================

echo "\n";
echo "==========================================\n";
echo " Footie app installed successfully!\n";
echo "==========================================\n";
echo "\n";
echo " App directory: {$appDir}/\n";
echo " Database:      {$config['db_name']}\n";
echo " Tables:        " . count($created) . " created";
if (count($skipped) > 0) {
    echo ", " . count($skipped) . " skipped";
}
echo "\n";

if (strtolower($loadSample) !== 'n' && file_exists(__DIR__ . '/sample-content.sql')) {
    // Reconnect to get counts
    $mysqli = new mysqli('localhost', $config['db_user'], $config['db_pass'], $config['db_name'], 0, $config['socket_path']);

    $seasons = $mysqli->query("SELECT COUNT(*) as count FROM seasons")->fetch_assoc()['count'] ?? 0;
    $teams = $mysqli->query("SELECT COUNT(*) as count FROM teams")->fetch_assoc()['count'] ?? 0;
    $leagues = $mysqli->query("SELECT COUNT(*) as count FROM leagues")->fetch_assoc()['count'] ?? 0;
    $cups = $mysqli->query("SELECT COUNT(*) as count FROM cups")->fetch_assoc()['count'] ?? 0;
    $leagueFixtures = $mysqli->query("SELECT COUNT(*) as count FROM league_fixtures")->fetch_assoc()['count'] ?? 0;
    $cupFixtures = $mysqli->query("SELECT COUNT(*) as count FROM cup_fixtures")->fetch_assoc()['count'] ?? 0;

    $mysqli->close();

    echo " Sample data:   Loaded\n";
    echo "   - {$seasons} season(s)\n";
    echo "   - {$teams} teams\n";
    echo "   - {$leagues} league(s)\n";
    echo "   - {$cups} cup(s)\n";
    echo "   - {$leagueFixtures} league fixtures\n";
    echo "   - {$cupFixtures} cup fixtures\n";
}
echo "\n";

// ============================================================================
// Update .env file
// ============================================================================

echo "Checking .env file...\n";

$envPath = __DIR__ . '/.env';
$currentEnv = file_exists($envPath) ? file_get_contents($envPath) : '';
$needsUpdate = false;
$newConfig = "";

// Check if Footie config already exists
if (strpos($currentEnv, 'ADMIN_PASSWORD_HASH') === false) {
    // Use admin password from options or default
    $defaultHash = password_hash('admin', PASSWORD_BCRYPT, ['cost' => 12]);
    $passwordHash = empty($adminPassword)
        ? $defaultHash
        : password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);

    $newConfig .= "\n\n# Footie App Configuration\n";
    $newConfig .= "# ======================\n";

    if (strpos($currentEnv, 'FOOTIE_DEBUG') === false) {
        $newConfig .= "FOOTIE_DEBUG=false\n";
    }

    $newConfig .= "ADMIN_PASSWORD_HASH={$passwordHash}\n";
    $newConfig .= "DB_NAME={$dbName}\n";
    $newConfig .= "DB_USER={$dbUser}\n";
    $newConfig .= "DB_PASS={$dbPass}\n";
    $needsUpdate = true;
} else {
    echo "  Footie configuration already exists in .env, skipping.\n";
}

// Append to .env if needed
if ($needsUpdate) {
    if (file_put_contents($envPath, $currentEnv . $newConfig)) {
        echo "  Updated .env with Footie configuration.\n";
    } else {
        echo "  Error: Failed to update .env file.\n";
    }
}

$adminPasswordDisplay = empty($adminPassword) ? "admin  ⚠️  CHANGE THIS IN PRODUCTION" : "[custom set]";
echo " Admin password: {$adminPasswordDisplay}\n";
echo "\n";
echo " Next steps:\n";
echo "   1. Start server: ./galvani\n";
echo "   2. Visit: http://localhost:8080/{$appDir}/\n";
echo "\n";
