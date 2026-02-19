#!/usr/bin/env sh
set -e

cd /var/www

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

# Cache framework metadata only in production to keep startup deterministic.
if [ "${APP_ENV:-production}" = "production" ]; then
  php artisan package:discover --ansi || true
  php artisan config:cache --ansi || true
  php artisan route:cache --ansi || true
  php artisan view:cache --ansi || true
fi

exec "$@"
