#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

echo "→ Stopping any existing dev servers on ports 8000–8002..."
pkill -f "artisan serve" 2>/dev/null || true
sleep 1

echo "→ Clearing caches..."
php artisan optimize:clear

echo "→ Ensuring database is ready..."
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --force

echo "→ Building frontend assets..."
if [ ! -f public/build/manifest.json ]; then
  NPM_CONFIG_CACHE="${NPM_CONFIG_CACHE:-.npm-cache}" npm run build
fi

PORT="${1:-8000}"
echo ""
echo "✓ Ready! Starting server at http://127.0.0.1:${PORT}"
echo "  Public site:  http://127.0.0.1:${PORT}/"
echo "  Admin login:  http://127.0.0.1:${PORT}/admin/login"
echo "  Email:        admin@diamondsteamcarwash.com"
echo "  Password:     password"
echo ""

php artisan serve --port="$PORT"
