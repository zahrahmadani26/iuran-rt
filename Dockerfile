# ============================================================
# Dockerfile Production untuk Laravel di Fly.io
# Menggunakan base image resmi serversideup (PHP 8.3 + Nginx)
# ============================================================
FROM serversideup/php:8.3-fpm-nginx

USER root

# Install ekstensi PHP GD yang dibutuhkan oleh PhpSpreadsheet / Excel
RUN install-php-extensions gd

# Set direktori kerja aplikasi
WORKDIR /var/www/html

# Environment default untuk container Laravel
ENV PHP_OPCACHE_ENABLE=1 \
    AUTORUN_LARAVEL_MIGRATION=false \
    AUTORUN_LARAVEL_STORAGE_LINK=false

# 1. Salin seluruh file aplikasi dengan kepemilikan 9999:9999 (www-data)
COPY --chown=9999:9999 . .

# 2. Install dependensi composer (tanpa dev)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# 3. Dump autoload & generate package discovery
RUN composer dump-autoload --optimize --no-dev

# 4. Salin script inisialisasi container saat boot ke entrypoint.d
COPY --chmod=755 docker/entrypoint.sh /etc/entrypoint.d/99-laravel-init.sh

# 5. Pastikan permissions folder storage, bootstrap/cache, dan database tepat
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chown -R 9999:9999 /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

USER root

EXPOSE 8080
