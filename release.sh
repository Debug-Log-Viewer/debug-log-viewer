#!/bin/bash

# Plugin directory (script should be inside the plugin folder)
PLUGIN_DIR="$(pwd)"
PLUGIN_NAME="$(basename "$PLUGIN_DIR")"
PARENT_DIR="$(dirname "$PLUGIN_DIR")"

# Find the main plugin file (first .php file in root)
PLUGIN_FILE=$(find . -maxdepth 1 -name "*.php" | head -n 1)

# Try extracting version from the plugin file
PLUGIN_VERSION=$(sed -nE 's/^.*Version:\s*([0-9.]+).*$/\1/p' "$PLUGIN_FILE")

# If version is empty, prompt the user
if [[ -z "$PLUGIN_VERSION" ]]; then
  echo "❓ Could not determine plugin version from $PLUGIN_FILE"
  read -p "🔹 Enter plugin version manually: " PLUGIN_VERSION
fi

# Validate version
if [[ -z "$PLUGIN_VERSION" ]]; then
  echo "❌ Error: Plugin version is required."
  exit 1
fi

# Define the output ZIP file
ZIP_NAME="${PLUGIN_NAME}-${PLUGIN_VERSION}.zip"
OUTPUT_PATH="${PARENT_DIR}/${ZIP_NAME}"

echo "📦 Preparing release: $ZIP_NAME..."

# Create the zip, excluding unnecessary files
zip -r "$OUTPUT_PATH" . \
    -x ".git/*" \
    -x "node_modules/*" \
    -x "*.gitignore" \
    -x "*.DS_Store" \
    -x "release.sh" \
    -x "package-lock.json" \
    -x "package.json" \
    -x "webpack.config.js" \
    -x "composer.json" \
    -x "composer.lock" \

echo "✅ Plugin packaged: $OUTPUT_PATH"
