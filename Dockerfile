# ============================================================
# Dockerfile Production untuk Laravel di Fly.io
# PHP 8.3 FPM + Nginx + Supervisor (Standard Debian Bookworm)
# ============================================================
FROM php:8.3-fpm-bookworm

# 1. Install sistem dependensi & ekstensi yang dibutuhkan
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_sqlite bcmath opcache zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Ambil binary Composer resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 3. Setup direktori kerja
WORKDIR /var/www/html

# 4. Salin seluruh source code project
COPY . .

# 5. Hapus cache lokal & install dependensi Composer production
RUN rm -f bootstrap/cache/*.php \
    && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts \
    && composer dump-autoload --optimize --no-dev

# 6. Salin konfigurasi Nginx, Supervisor, dan Entrypoint
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# 7. Pastikan hak akses direktori storage dan cache
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
