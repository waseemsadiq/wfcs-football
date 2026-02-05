#!/bin/bash
# Build script for Shared Hosting Distribution

# Ensure we are in the script directory
cd "$(dirname "$0")"

echo "🦇 Building dist folder for shared hosting..."

# Clean previous build
rm -rf dist
mkdir dist

# Copy app files (excluding dev stuff)
rsync -a . dist/ \
  --exclude 'dist' \
  --exclude 'node_modules' \
  --exclude '.git' \
  --exclude '.agent' \
  --exclude '.claude' \
  --exclude 'tests' \
  --exclude '.DS_Store' \
  --exclude 'archive' \
  --exclude '.env'

# Copy shared config files over the defaults
echo "Applying shared hosting configurations..."

# App Config
if [ -f "dist/config/app-shared.php" ]; then
    mv dist/config/app-shared.php dist/config/app.php
elif [ -f "dist/config/app-shared.example.php" ]; then
    mv dist/config/app-shared.example.php dist/config/app.php
    echo "⚠️  Using example app config (missing app-shared.php)"
fi

# DB Config
if [ -f "dist/config/database-shared.php" ]; then
    mv dist/config/database-shared.php dist/config/database.php
elif [ -f "dist/config/database-shared.example.php" ]; then
    mv dist/config/database-shared.example.php dist/config/database.php
    echo "⚠️  Using example database config (missing database-shared.php)"
fi

mv dist/core/Database-shared.php dist/core/Database.php
mv dist/htaccess-shared dist/.htaccess

# Copy SQL file from root (source of truth)
if [ -f "../sample-content.sql" ]; then
    cp ../sample-content.sql dist/footie-shared-install.sql
    echo "Included SQL installer (fresh copy)."
fi

echo "✅ Build complete!"
echo "📂 Distribution ready in: dist/"
echo ""
echo "Next steps:"
echo "1. Edit dist/config/app.php (Add Admin Password Hash)"
echo "2. Edit dist/config/database.php (Add DB Credentials)"
echo "3. Upload contents of dist/ to public_html/footie/"
