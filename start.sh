#!/bin/bash
set -e

# Hapus service yang tidak ada
# service rsyslog start
# service fail2ban start

# Jalankan PM2 untuk app Node.js (jika ada process.yml)
if [ -f /var/www/html/process.yml ]; then
  pm2-runtime /var/www/html/process.yml
fi

# Jalankan PHP-FPM di foreground
exec php-fpm -F
