#!/bin/sh
set -e

echo "Starting NeoERP container..."

# Ensure storage directories exist and are writable
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache database
chmod -R 775 storage bootstrap/cache database
chown -R www-data:www-data storage bootstrap/cache database

# Ensure SQLite file exists if using sqlite
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    if [ ! -f database/database.sqlite ]; then
        echo "Creating SQLite database..."
        touch database/database.sqlite
        chown www-data:www-data database/database.sqlite
        chmod 775 database/database.sqlite
        echo "Running database migrations..."
        php artisan migrate --force || true
        echo "Seeding database..."
        php artisan db:seed --force || true
    else
        echo "Running database migrations on existing database..."
        php artisan migrate --force || true
    fi
    chmod 775 database/database.sqlite
    chown www-data:www-data database/database.sqlite
fi

# Cache configuration, routes, and views if in production
if [ "$APP_ENV" = "production" ]; then
    echo "Caching configurations..."
    php artisan config:clear || true
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm -D

# Start Nginx in foreground
echo "Starting Nginx on port 8080..."
exec nginx -g "daemon off;"
