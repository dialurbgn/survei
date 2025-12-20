FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    libxml2-dev libonig-dev zlib1g-dev libxrender1 libfontconfig1 \
    ghostscript libicu-dev libmagickwand-dev \
    libpq-dev \
    fail2ban \
    rsyslog \
    gnupg ca-certificates build-essential \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql pdo_mysql zip gd mbstring exif pcntl intl

# Imagick (for Browsershot)
RUN pecl install imagick && docker-php-ext-enable imagick

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js 20.x + PM2
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g pm2 && \
    rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy fail2ban configuration first
COPY fail2ban /etc/fail2ban

# Copy entrypoint and start scripts
COPY entrypoint.sh /entrypoint.sh
COPY start.sh /start.sh
RUN chmod +x /entrypoint.sh /start.sh

# Copy application files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    mkdir -p /var/www/html/writable && \
    chmod -R 777 /var/www/html/writable

# Expose port
EXPOSE 9000

# Set entrypoint (akan execute start.sh)
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm", "-F"]