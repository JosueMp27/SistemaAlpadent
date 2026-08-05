#!/bin/sh
set -e

# Clear configuration cache on boot
php artisan config:clear || true
php artisan route:clear || true

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
