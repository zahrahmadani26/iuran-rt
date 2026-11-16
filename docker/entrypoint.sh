#!/bin/sh
set -e

echo "🚀 [Fly.io] Starting Laravel Application on Fly.io..."

# Pastikan direktori database ada
mkdir -p /var/www/html/database
chmod 775 /var/www/html/database

# Inisialisasi SQLite database jika belum ada
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "📄 [Fly.io] Membuat database SQLite baru..."
    touch /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
    echo "🗄️ [Fly.io] Menjalankan migrasi dan seeder awal..."
    php /var/www/html/artisan migrate --force --seed
else
    echo "🗄️ [Fly.io] Menjalankan migrasi database..."
    php /var/www/html/artisan migrate --force
fi

# Pastikan symbolic link storage terpasang
php /var/www/html/artisan storage:link || true

# Optimasi konfigurasi, route, dan view cache
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache

echo "✅ [Fly.io] Laravel siap dijalankan!"
