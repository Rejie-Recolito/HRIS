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

# Substitute nginx template and start services
: ${PORT:=8080}
export PORT
envsubst '$PORT' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf || true

# Start php-fpm (daemon) then nginx in foreground
php-fpm -D
nginx -g 'daemon off;'
