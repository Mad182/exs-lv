#!/bin/sh
set -e

# Run composer install if vendor/autoload.php is missing
if [ ! -f /var/www/exs-lv/vendor/autoload.php ]; then
    echo "vendor/autoload.php missing, running composer install..."
    cd /var/www/exs-lv && composer install --no-dev --prefer-dist --no-interaction || true
fi

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
exec nginx -g "daemon off;"
