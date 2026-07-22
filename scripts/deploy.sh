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

command -v rsync >/dev/null || {
  printf 'rsync is required locally to deploy the theme.\n' >&2
  exit 1
}

cd "$THEME_DIR"
npm run build

rsync "${RSYNC_OPTIONS[@]}" \
  -e "ssh -p $REMOTE_PORT" \
  "$THEME_DIR/" \
  "$REMOTE_HOST:$REMOTE_THEME_DIR"
