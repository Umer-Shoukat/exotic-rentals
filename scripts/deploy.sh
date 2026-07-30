#!/usr/bin/env bash

set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
REMOTE_HOST="u943866697@46.202.182.18"
REMOTE_PORT="65002"
REMOTE_THEME_DIR="/home/u943866697/domains/echelonmotions.net/public_html/wp-content/themes/car-rental-theme/"

RSYNC_OPTIONS=(
  --archive
  --compress
  --verbose
  --exclude=.git/
  --exclude=node_modules/
  --exclude=.DS_Store
)

if [[ "${1:-}" == "--dry-run" ]]; then
  RSYNC_OPTIONS+=(--dry-run)
elif [[ $# -gt 0 ]]; then
  printf 'Usage: npm run deploy -- [--dry-run]\n' >&2
  exit 2
fi

command -v npm >/dev/null || {
  printf 'npm is required locally to build the production assets.\n' >&2
  exit 1
}

cd "$THEME_DIR"
npm run build

if command -v rsync >/dev/null; then
  rsync "${RSYNC_OPTIONS[@]}" \
    -e "ssh -p $REMOTE_PORT" \
    "$THEME_DIR/" \
    "$REMOTE_HOST:$REMOTE_THEME_DIR"
else
  command -v tar >/dev/null || {
    printf 'rsync is not installed, and tar is required for the fallback deploy.\n' >&2
    exit 1
  }

  command -v ssh >/dev/null || {
    printf 'rsync is not installed, and ssh is required for the fallback deploy.\n' >&2
    exit 1
  }

  TAR_OPTIONS=(
    --exclude=.git
    --exclude=node_modules
    --exclude=.DS_Store
  )

  if [[ "${1:-}" == "--dry-run" ]]; then
    printf 'rsync is not installed; validating fallback archive only.\n'
    tar -czf /dev/null "${TAR_OPTIONS[@]}" -C "$THEME_DIR" .
    exit 0
  fi

  printf 'rsync is not installed; deploying with tar over ssh.\n'
  ssh -p "$REMOTE_PORT" "$REMOTE_HOST" "mkdir -p '$REMOTE_THEME_DIR'"
  tar -czf - "${TAR_OPTIONS[@]}" -C "$THEME_DIR" . | ssh -p "$REMOTE_PORT" "$REMOTE_HOST" "tar -xzf - -C '$REMOTE_THEME_DIR'"
fi
