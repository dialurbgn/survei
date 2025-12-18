#!/bin/bash
set -e

# Start rsyslog dan fail2ban
service rsyslog start
service fail2ban start

# Jalankan PM2 untuk app Node.js (jika ada process.yml)
if [ -f /var/www/html/process.yml ]; then
  pm2-runtime /var/www/html/process.yml
fi

# Jalankan PHP-FPM sebagai PID 1 di foreground
exec php-fpm -F
