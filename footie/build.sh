#!/bin/bash
# Build script for Shared Hosting Distribution

# Ensure we are in the script directory
cd "$(dirname "$0")"

echo "🦇 Building dist folder for shared hosting..."

# Clean previous build (BUT preserve config!)
if [ -f "dist/config/app.php" ]; then
    cp dist/config/app.php /tmp/footie_app_config_backup.php
fi
if [ -f "dist/config/database.php" ]; then
    cp dist/config/database.php /tmp/footie_db_config_backup.php
fi

rm -rf dist
mkdir dist

# Copy app files (only runtime essentials)
rsync -a . dist/ \
  --exclude 'dist' \
  --exclude 'node_modules' \
  --exclude '.git' \
  --exclude '.agent' \
  --exclude '.claude' \
  --exclude '.wiki' \
  --exclude '.worktrees' \
  --exclude 'tests' \
  --exclude '.DS_Store' \
  --exclude 'archive' \
  --exclude 'docs' \
  --exclude 'data' \
  --exclude '.env' \
  --exclude '.gitignore' \
  --exclude '.gitattributes' \
  --exclude 'package.json' \
  --exclude 'package-lock.json' \
  --exclude 'tailwind.config.js' \
  --exclude 'generate-icons.js' \
  --exclude 'create-package.php' \
  --exclude 'footie-install.php' \
  --exclude 'generate_password.php' \
  --exclude 'wp-install.php' \
  --exclude 'build.sh' \
  --exclude 'sync-wiki.sh' \
  --exclude '*.md' \
  --exclude '*.sql'

# Create empty data directory (we excluded the local one)
mkdir -p dist/data
echo "Deny from all" > dist/data/.htaccess

# Copy shared config files over the defaults
echo "Applying shared hosting configurations..."

# App Config
if [ -f "/tmp/footie_app_config_backup.php" ]; then
    mv /tmp/footie_app_config_backup.php dist/config/app.php
    echo "♻️  Restored existing app.php (preserved admin password)"
elif [ -f "dist/config/app-shared.php" ]; then
    mv dist/config/app-shared.php dist/config/app.php
elif [ -f "dist/config/app-shared.example.php" ]; then
    mv dist/config/app-shared.example.php dist/config/app.php
    echo "⚠️  Using example app config (missing app-shared.php)"
fi

# DB Config
if [ -f "/tmp/footie_db_config_backup.php" ]; then
    mv /tmp/footie_db_config_backup.php dist/config/database.php
    echo "♻️  Restored existing database.php (preserved credentials)"
elif [ -f "dist/config/database-shared.php" ]; then
    mv dist/config/database-shared.php dist/config/database.php
elif [ -f "dist/config/database-shared.example.php" ]; then
    mv dist/config/database-shared.example.php dist/config/database.php
    echo "⚠️  Using example database config (missing database-shared.php)"
fi

# Cleanup shared templates from dist (we don't want them in live)
rm -f dist/config/app-shared.php
rm -f dist/config/database-shared.php
rm -f dist/config/app-shared.example.php
rm -f dist/config/database-shared.example.php

mv dist/core/Database-shared.php dist/core/Database.php


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
