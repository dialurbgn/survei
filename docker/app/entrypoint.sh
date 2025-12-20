#!/bin/bash
set -e

echo "=== Entrypoint: Starting initialization ==="

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

# Create necessary directories
echo "Creating directories..."
mkdir -p /var/www/html/log
mkdir -p /var/www/html/logs
mkdir -p /var/www/html/application/session

# Set writable permissions
chmod -R 775 /var/www/html/log

# Install composer dependencies if needed (development mode)
if [ -f /var/www/html/composer.json ] && [ ! -d /var/www/html/vendor ]; then
    echo "Installing composer dependencies..."
    cd /var/www/html
    composer install --no-interaction --optimize-autoloader
fi

# Start rsyslog for fail2ban
if command -v rsyslogd &> /dev/null; then
    echo "Starting rsyslog..."
    rsyslogd || true
fi

# Start fail2ban
if command -v fail2ban-server &> /dev/null; then
    echo "Starting fail2ban..."
    service fail2ban start || true
fi

# Run start.sh if exists (PM2 + PHP-FPM)
if [ -f /start.sh ]; then
    echo "Executing start.sh..."
    exec /start.sh
fi

# Fallback: Execute the main command
echo "Starting PHP-FPM (fallback)..."
exec "$@"