#!/bin/bash
set -e

# Jalankan Node.js PM2 jika ada
if [ -f /var/www/html/process.yml ]; then
  echo "Starting PM2 with process.yml..."
  pm2-runtime /var/www/html/process.yml &
fi

# Jalankan PHP-FPM di foreground agar container tetap hidup
echo "Starting PHP-FPM in foreground..."
exec php-fpm -F