#!/bin/sh
set -e

# Jalankan PHP-FPM
php-fpm -R

# Jalankan perintah lain jika ada
exec "$@"
