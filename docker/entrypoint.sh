#!/usr/bin/env sh
set -e

# Cache configuration, routes and views using the runtime environment variables.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Apply database migrations on boot. Safe to run repeatedly.
php artisan migrate --force

exec "$@"
