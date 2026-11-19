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

# Pakai database eksternal bila DATABASE_URL tersedia.
# Jika tidak ada, pakai SQLite agar deploy tetap jalan tanpa variabel tambahan.
if [ -n "$DATABASE_URL" ]; then
    case "$DATABASE_URL" in
        postgres://*|postgresql://*)
            export DB_URL="$DATABASE_URL"
            export DB_CONNECTION=pgsql
            ;;
        *)
            unset DATABASE_URL DB_URL
            export DB_CONNECTION=sqlite
            export DB_DATABASE=/var/www/html/database/database.sqlite
            mkdir -p /var/www/html/database
            touch /var/www/html/database/database.sqlite
            ;;
    esac
else
    unset DB_URL
    export DB_CONNECTION=sqlite
    export DB_DATABASE=/var/www/html/database/database.sqlite
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
fi

php artisan migrate --force

# Isi data demo sekali saja saat database masih kosong.
php /var/www/html/docker/seed-if-empty.php

php artisan config:clear || true
php artisan view:clear || true

exec "$@"
