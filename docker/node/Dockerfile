# Gunakan image Node.js resmi versi LTS
FROM node:18-alpine

# Install PHP 8.3 dan dependencies sistem yang diperlukan
RUN apk add --no-cache \
    php83 \
    php83-cli \
    php83-phar \
    php83-mbstring \
    php83-openssl \
    php83-json \
    php83-curl \
    php83-dom \
    php83-xml \
    php83-xmlreader \
    php83-xmlwriter \
    php83-tokenizer \
    php83-ctype \
    php83-session \
    php83-zip \
    php83-mysqli \
    php83-pdo \
    php83-pdo_mysql \
    bash \
    curl

# Buat symlink untuk php agar bisa dipanggil dengan 'php'
RUN ln -sf /usr/bin/php83 /usr/bin/php

# Buat direktori kerja
WORKDIR /usr/src/app

# Install PM2 global
RUN npm install -g pm2

# Salin semua file ke container
COPY . .

# Buat user non-root untuk security (optional)
RUN addgroup -g 1001 -S nodejs && \
    adduser -S nodeuser -u 1001 -G nodejs

# Set permission untuk direktori kerja
RUN chown -R nodeuser:nodejs /usr/src/app
USER nodeuser

# Expose port yang akan dipakai
EXPOSE 8000

# Health check (optional)
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

# Jalankan pm2 sesuai process.yml
CMD ["pm2-runtime", "process.yml"]