#!/bin/sh
set -e

cd /var/www/html

php artisan migrate --force

# Isi data demo sekali saja saat database masih kosong.
php /var/www/html/docker/seed-if-empty.php

php artisan config:clear || true
php artisan view:clear || true

exec "$@"
