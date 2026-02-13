#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
RELEASE_DIR="$ROOT_DIR/release"

echo "Preparing release directory: $RELEASE_DIR"
rm -rf "$RELEASE_DIR"
mkdir -p "$RELEASE_DIR"

copy_if_exists() {
  local source_path="$1"
  if [ -e "$ROOT_DIR/$source_path" ]; then
    cp -R "$ROOT_DIR/$source_path" "$RELEASE_DIR/"
    echo "Included: $source_path"
  fi
}

# Core app files
copy_if_exists "index.php"
copy_if_exists ".htaccess"
copy_if_exists "composer.json"
copy_if_exists "composer.lock"

# Runtime folders (PHP + assets)
copy_if_exists "api"
copy_if_exists "config"
copy_if_exists "controllers"
copy_if_exists "models"
copy_if_exists "servidor"
copy_if_exists "src"
copy_if_exists "static"
copy_if_exists "vendor"

# Enforce static as assets-only in release package.
if [ -d "$RELEASE_DIR/static" ]; then
  find "$RELEASE_DIR/static" -type f -name "*.php" -delete
  echo "Removed PHP files from release/static"
fi

echo "Release is ready in: $RELEASE_DIR"
