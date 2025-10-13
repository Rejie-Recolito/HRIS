#!/bin/sh
# Minimal startup helper for Laravel in container
# Ensures database file exists and APP_KEY is set before starting php-fpm and nginx

set -e

APP_DIR=/var/www/html
cd "$APP_DIR"

# Ensure storage and bootstrap dirs exist
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database
chown -R www-data:www-data storage bootstrap/cache database || true
chmod -R 755 storage bootstrap/cache || true

# Ensure sqlite database exists if DB_CONNECTION=sqlite
if [ "${DB_CONNECTION:-}" = "sqlite" ] || [ -z "${DB_CONNECTION:-}" ]; then
  DB_PATH=${DB_DATABASE:-/var/www/html/database/database.sqlite}
  if [ ! -f "$DB_PATH" ]; then
    echo "Creating sqlite database at $DB_PATH"
    mkdir -p "$(dirname "$DB_PATH")"
    touch "$DB_PATH"
    chmod 666 "$DB_PATH" || true
  fi
fi

# Generate APP_KEY if missing (only when APP_KEY is empty)
if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY missing — generating temporary key"
  # generate into .env if present or use artisan to set
  if [ -f .env ]; then
    php artisan key:generate --force
  else
    # create a minimal .env with APP_KEY
    php artisan key:generate --force
  fi
fi

# Clear any cached config/routes/views to ensure runtime uses current env
echo "Clearing Laravel caches (config, route, view)"
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Only run cache:clear when cache driver is not database-backed. If the app uses
# a database cache store and the cache table hasn't been migrated yet, running
# cache:clear will throw "no such table: cache" and fail startup.
CACHE_DRIVER=${CACHE_DRIVER:-${CACHE_STORE:-}}
if [ "${CACHE_DRIVER}" = "database" ]; then
  echo "Skipping php artisan cache:clear because CACHE_DRIVER/CACHE_STORE is set to 'database'.\nIf you haven't run migrations yet, run 'php artisan cache:table' then 'php artisan migrate' or set CACHE_DRIVER=file temporarily."
else
  echo "Clearing cache repository"
  php artisan cache:clear || true
fi

# Substitute nginx template and start services
: ${PORT:=8080}
export PORT
envsubst '$PORT' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf || true

# Dump last lines of Laravel log to stdout to help debugging when LOG_CHANNEL isn't configured
if [ -f storage/logs/laravel.log ]; then
  echo "--- Laravel log (last 200 lines) ---"
  tail -n 200 storage/logs/laravel.log || true
  echo "--- end laravel.log ---"
fi
# Start php-fpm (daemon) then nginx in foreground
php-fpm -D
nginx -g 'daemon off;'
