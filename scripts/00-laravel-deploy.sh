#!/usr/bin/env bash
echo "Menjalankan migrasi database..."
( php /var/www/html/artisan migrate --force ) || true

echo "Melakukan cache konfigurasi & route..."
( php /var/www/html/artisan config:cache ) || true
( php /var/www/html/artisan route:cache ) || true
( php /var/www/html/artisan view:cache ) || true

echo "Membuat symlink storage..."
( php /var/www/html/artisan storage:link ) || true