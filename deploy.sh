#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="$HOME/htdocs/www.unifiedcoreindia.com"
cd "$APP_DIR"

echo "🚀 Starting deployment..."

git pull --ff-only origin main
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
npm ci --no-audit --no-fund
npm run build

php artisan optimize:clear
php artisan migrate --force
php artisan optimize

echo "✅ Deployment finished!"