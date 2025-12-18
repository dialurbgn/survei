#!/bin/bash

# Jalankan Node.js PM2 jika ada
if [ -f /var/www/html/process.yml ]; then
  pm2-runtime /var/www/html/process.yml &
fi

# Jalankan PHP-FPM di foreground agar container tetap hidup
exec php-fpm -F
