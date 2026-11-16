#!/bin/sh
set -e

echo "🚀 [Fly.io] Initializing Laravel..."

# Pastikan folder data ada di persistent volume
mkdir -p /data
chown -R www-data:www-data /data
chmod -R 775 /data

# Jalankan migrasi dan seeder
if [ ! -f /data/database.sqlite ]; then
    echo "📄 Membuat file database.sqlite baru di /data..."
    touch /data/database.sqlite
    chown www-data:www-data /data/database.sqlite
    chmod 664 /data/database.sqlite
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

# Pastikan permissions folder storage, bootstrap/cache, dan data tepat
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /data
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /data

echo "✅ [Fly.io] Inisialisasi selesai! Menjalankan Nginx & PHP-FPM..."

exec "$@"
