#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

# Generate an app key only if one isn't already set
if ! grep -q "^APP_KEY=base64" .env; then
    php artisan key:generate --force
fi

php artisan migrate --force

chown -R www-data:www-data storage bootstrap/cache database

exec "$@"
