#!/bin/sh
set -e

cd /var/www/html

# Railway (dan sebagian platform lain) menentukan port lewat env PORT.
# Apache bawaan image php:apache listen di 80, jadi sesuaikan pada saat start.
PORT="${PORT:-80}"
if [ "$PORT" != "80" ]; then
    sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
    sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/*.conf
fi

# Railway/Neon menyediakan DATABASE_URL, sementara Laravel membaca DB_URL.
if [ -z "$DB_URL" ] && [ -n "$DATABASE_URL" ]; then
    export DB_URL="$DATABASE_URL"
    export DB_CONNECTION="${DB_CONNECTION:-pgsql}"
fi

php artisan migrate --force

# Isi data demo sekali saja saat database masih kosong.
php /var/www/html/docker/seed-if-empty.php

php artisan config:clear || true
php artisan view:clear || true

exec "$@"
