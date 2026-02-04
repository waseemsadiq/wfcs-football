#!/usr/bin/env php
<?php
/**
 * Footie App Package Creator
 *
 * Creates a distributable zip package containing:
 * - footie/ directory (excluding archive/, docs/, data/, .DS_Store)
 * - footie-install.php installer
 * - sample-content.sql database dump
 *
 * Usage: ./galvani create-package.php --version=1.0
 */

// Parse command line options
$options = getopt('hv:', [
    'help',
    'version:'
]);

if (isset($options['h']) || isset($options['help'])) {
    echo "Footie App Package Creator\n";
    echo "\nUsage: ./galvani create-package.php --version=VERSION\n\n";
    echo "Options:\n";
    echo "  --version=VERSION  Package version (e.g., 1.0)\n";
    echo "  -h, --help         Show this help message\n\n";
    exit(0);
}

$version = $options['v'] ?? $options['version'] ?? null;
if (!$version) {
    echo "Error: --version is required\n";
    echo "Usage: ./galvani create-package.php --version=1.0\n";
    exit(1);
}

// Validate version format
if (!preg_match('/^\d+\.\d+(\.\d+)?$/', $version)) {
    echo "Error: Version must be in format X.Y or X.Y.Z (e.g., 1.0 or 1.0.1)\n";
    exit(1);
}

// Paths
$rootDir = __DIR__;
$footieDir = $rootDir . '/footie';
$installerFile = $rootDir . '/footie-install.php';
$installMdFile = $rootDir . '/INSTALL.md';
$mysqlSocket = $rootDir . '/data/mysql.sock';
$tempDir = sys_get_temp_dir() . '/footie-package-' . uniqid();
$outputZip = $rootDir . "/footie-app-v{$version}.zip";

echo "===========================================\n";
echo " Footie App Package Creator v{$version}\n";
echo "===========================================\n\n";

// Preflight checks
echo "[1/7] Checking prerequisites...\n";

if (!file_exists($footieDir)) {
    echo "  ✗ Error: footie/ directory not found\n";
    exit(1);
}

if (!file_exists($installerFile)) {
    echo "  ✗ Error: footie-install.php not found\n";
    exit(1);
}

if (!file_exists($installMdFile)) {
    echo "  ✗ Error: INSTALL.md not found\n";
    exit(1);
}

if (!file_exists($mysqlSocket)) {
    echo "  ✗ Error: MySQL socket not found at {$mysqlSocket}\n";
    echo "    Make sure MySQL is running: ./galvani --mysql\n";
    exit(1);
}

// Test MySQL connection
$mysqli = @new mysqli('localhost', 'root', '', 'wfcs', 0, $mysqlSocket);
if ($mysqli->connect_error) {
    echo "  ✗ Error: Cannot connect to MySQL: {$mysqli->connect_error}\n";
    exit(1);
}
$mysqli->close();

echo "  ✓ All prerequisites met\n\n";

// Step 2: Export database
echo "[2/7] Exporting database to sample-content.sql...\n";

$sqlFile = $tempDir . '/sample-content.sql';
mkdir($tempDir, 0755, true);

// Using escapeshellarg for safe shell execution
$command = sprintf(
    'mysqldump -u root --socket=%s --skip-ssl wfcs > %s 2>&1',
    escapeshellarg($mysqlSocket),
    escapeshellarg($sqlFile)
);

exec($command, $output, $returnCode);

if ($returnCode !== 0) {
    echo "  ✗ Error: mysqldump failed\n";
    echo "    " . implode("\n    ", $output) . "\n";
    exec("rm -rf " . escapeshellarg($tempDir));
    exit(1);
}

if (!file_exists($sqlFile) || filesize($sqlFile) === 0) {
    echo "  ✗ Error: SQL dump is empty or not created\n";
    exec("rm -rf " . escapeshellarg($tempDir));
    exit(1);
}

$sqlSize = filesize($sqlFile);
echo "  ✓ Database exported (" . number_format($sqlSize / 1024, 2) . " KB)\n";

// Strip AUTO_INCREMENT values from the dump
$sqlContent = file_get_contents($sqlFile);
$sqlContent = preg_replace('/\s+AUTO_INCREMENT=\d+/', '', $sqlContent);
file_put_contents($sqlFile, $sqlContent);
echo "  ✓ Stripped AUTO_INCREMENT values from SQL dump\n";

// Update size after modification
$sqlSize = filesize($sqlFile);

// Update the root sample-content.sql file so it's committed to git
if (copy($sqlFile, $rootDir . '/sample-content.sql')) {
    echo "  ✓ Updated project root sample-content.sql\n\n";
} else {
    echo "  ! Warning: Could not update project root sample-content.sql\n\n";
}

// Step 3: Copy footie directory (with exclusions)
echo "[3/7] Copying footie/ directory...\n";

$footieTempDir = $tempDir . '/footie';
mkdir($footieTempDir, 0755, true);

$excludeDirs = ['archive', 'docs', 'data', 'node_modules'];
$excludeFiles = ['.DS_Store'];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($footieDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$copiedFiles = 0;
$skippedFiles = 0;

foreach ($iterator as $item) {
    $relativePath = substr($item->getPathname(), strlen($footieDir) + 1);
    $targetPath = $footieTempDir . '/' . $relativePath;

    // Check exclusions
    $skip = false;

    // Skip excluded directories
    foreach ($excludeDirs as $excludeDir) {
        if (str_starts_with($relativePath, $excludeDir . '/') || $relativePath === $excludeDir) {
            $skip = true;
            break;
        }
    }

    // Skip excluded files
    foreach ($excludeFiles as $excludeFile) {
        if (str_ends_with($relativePath, $excludeFile)) {
            $skip = true;
            break;
        }
    }

    if ($skip) {
        $skippedFiles++;
        continue;
    }

    if ($item->isDir()) {
        mkdir($targetPath, 0755, true);
    } else {
        copy($item->getPathname(), $targetPath);
        $copiedFiles++;
    }
}

echo "  ✓ Copied {$copiedFiles} files (skipped {$skippedFiles})\n\n";

// Step 4: Copy installer and documentation
echo "[4/7] Copying footie-install.php and INSTALL.md...\n";
copy($installerFile, $tempDir . '/footie-install.php');
copy($installMdFile, $tempDir . '/INSTALL.md');
echo "  ✓ Installer and documentation copied\n\n";

// Step 5: Create zip archive
echo "[5/7] Creating zip archive...\n";

if (file_exists($outputZip)) {
    unlink($outputZip);
}

$zip = new ZipArchive();
if ($zip->open($outputZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    echo "  ✗ Error: Cannot create zip file\n";
    exec("rm -rf " . escapeshellarg($tempDir));
    exit(1);
}

// Add files to zip (strip temp directory prefix)
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$zipFiles = 0;
$tempDirLen = strlen($tempDir);

foreach ($files as $file) {
    $filePath = $file->getPathname();
    // Get path relative to temp directory
    $relativePath = substr($filePath, $tempDirLen + 1);

    if ($file->isDir()) {
        $zip->addEmptyDir($relativePath . '/');
    } else {
        $zip->addFile($filePath, $relativePath);
        $zipFiles++;
    }
}

$zip->close();
echo "  ✓ Archive created with {$zipFiles} files\n\n";

// Step 6: Cleanup
echo "[6/7] Cleaning up temporary files...\n";
exec("rm -rf " . escapeshellarg($tempDir));
echo "  ✓ Temporary files removed\n\n";

// Step 7: Display summary
echo "[7/7] Package summary:\n";
echo "\n";
echo "  Package:       footie-app-v{$version}.zip\n";
echo "  Location:      {$outputZip}\n";
echo "  Size:          " . number_format(filesize($outputZip) / 1024 / 1024, 2) . " MB\n";
echo "\n";
echo "  Contents:\n";
echo "    • footie/                 ({$copiedFiles} files)\n";
echo "    • footie-install.php      (installer)\n";
echo "    • INSTALL.md              (installation guide)\n";
echo "    • sample-content.sql      (" . number_format($sqlSize / 1024, 2) . " KB)\n";
echo "\n";
echo "  Excluded:\n";
echo "    • footie/archive/\n";
echo "    • footie/docs/\n";
echo "    • footie/data/\n";
echo "    • footie/node_modules/\n";
echo "    • .DS_Store files\n";
echo "\n";

// Verify SQL content
$mysqli = new mysqli('localhost', 'root', '', 'wfcs', 0, $mysqlSocket);

// Count sample data
$teams = $mysqli->query("SELECT COUNT(*) as count FROM teams")->fetch_assoc()['count'];
$seasons = $mysqli->query("SELECT COUNT(*) as count FROM seasons")->fetch_assoc()['count'];
$leagues = $mysqli->query("SELECT COUNT(*) as count FROM leagues")->fetch_assoc()['count'];
$cups = $mysqli->query("SELECT COUNT(*) as count FROM cups")->fetch_assoc()['count'];
$leagueFixtures = $mysqli->query("SELECT COUNT(*) as count FROM league_fixtures")->fetch_assoc()['count'];
$cupFixtures = $mysqli->query("SELECT COUNT(*) as count FROM cup_fixtures")->fetch_assoc()['count'];
$fixtures = $leagueFixtures + $cupFixtures;

$mysqli->close();

echo "  Sample data:\n";
echo "    • {$seasons} season(s)\n";
echo "    • {$teams} teams\n";
echo "    • {$leagues} league(s)\n";
echo "    • {$cups} cup(s)\n";
echo "    • {$fixtures} fixtures\n";
echo "\n";

echo "===========================================\n";
echo " ✓ Package created successfully!\n";
echo "===========================================\n";
echo "\n";
echo "Test the package:\n";
echo "  unzip footie-app-v{$version}.zip -d test-install\n";
echo "  cd test-install\n";
echo "  ./galvani footie-install.php\n";
echo "\n";
