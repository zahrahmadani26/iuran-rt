# ============================================================
# Dockerfile Production untuk Laravel di Fly.io
# Menggunakan base image resmi serversideup (PHP 8.3 + Nginx)
# ============================================================
FROM serversideup/php:8.3-fpm-nginx

# Set direktori kerja aplikasi
WORKDIR /var/www/html

# Environment default untuk container Laravel
ENV PHP_OPCACHE_ENABLE=1 \
    AUTORUN_LARAVEL_MIGRATION=false \
    AUTORUN_LARAVEL_STORAGE_LINK=false

# 1. Salin konfigurasi dependensi Composer terlebih dahulu untuk cache layer
COPY --chown=9999:9999 composer.json composer.lock ./

# 2. Install dependensi composer (tanpa dev)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# 3. Salin seluruh file aplikasi
COPY --chown=9999:9999 . .

# 4. Dump autoload & generate package discovery
RUN composer dump-autoload --optimize --no-dev

# 5. Salin script inisialisasi container saat boot ke entrypoint.d
COPY --chmod=755 docker/entrypoint.sh /etc/entrypoint.d/99-laravel-init.sh

# 6. Pastikan permissions folder storage, bootstrap/cache, dan database tepat
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

EXPOSE 8080
