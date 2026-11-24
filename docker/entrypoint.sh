#!/bin/sh
set -e

echo "🚀 [Deploy] Initializing Laravel..."

# If PORT is specified (e.g. Render uses 10000 or custom port), update Nginx config
PORT=${PORT:-8080}
echo "🌐 Configuring Nginx to listen on port $PORT..."
sed -i "s/listen [0-9]\+ default_server;/listen $PORT default_server;/g" /etc/nginx/sites-available/default
sed -i "s/listen \[::\]:[0-9]\+ default_server;/listen [::]:$PORT default_server;/g" /etc/nginx/sites-available/default

# Pastikan folder data ada
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

echo "✅ [Deploy] Inisialisasi selesai! Menjalankan Nginx & PHP-FPM..."

exec "$@"
