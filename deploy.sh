#!/bin/bash
# ============================================================
# Script Deployment Manual untuk VPS Ubuntu / Debian
# Cara pakai: chmod +x deploy.sh && ./deploy.sh
# ============================================================

set -e

echo "🚀 [SI-IURAN RT] Memulai proses deployment..."

# 1. Maintenance mode
php artisan down || true

# 2. Update repository
echo "📥 Menarik perubahan terbaru dari git origin/main..."
git pull origin main

# 3. Install composer dependensi production
echo "📦 Menginstall dependensi Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 4. Migrasi Database
echo "🗄️ Menjalankan migrasi database..."
php artisan migrate --force

# 5. Clear & Re-cache
echo "⚡ Mengoptimalkan cache konfigurasi & view..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Storage link
php artisan storage:link || true

# 7. Set permission direktori storage & bootstrap cache
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 8. Matikan mode maintenance
php artisan up

echo "🎉 [SELESAI] Project berhasil diperbarui dan aktif kembali!"
