#!/bin/sh
set -e

echo "🚀 [Fly.io] Initializing Laravel..."

# Pastikan folder database ada di persistent volume
mkdir -p /var/www/html/database
chown -R www-data:www-data /var/www/html/database
chmod -R 775 /var/www/html/database

if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "📄 Membuat file database.sqlite baru..."
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
    echo "🗄️ Menjalankan migrasi dan seeder awal..."
    php artisan migrate --force --seed
else
    echo "🗄️ Menjalankan migrasi database..."
    php artisan migrate --force
fi

php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Pastikan permissions folder storage dan database tepat
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

echo "✅ [Fly.io] Inisialisasi selesai! Menjalankan Nginx & PHP-FPM..."

exec "$@"
