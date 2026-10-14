#!/bin/sh
set -e
cd /app

# 1. App key (kalau belum diset lewat env platform)
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

# 2. Optimasi konfigurasi
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

# 3. Migrasi database
php artisan migrate --force

# 4. Seed SEKALI saja (hanya kalau tabel users masih kosong),
#    supaya data yang sudah diinput tidak pernah terhapus saat server restart
php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (\App\Models\User::count() === 0) {
    Illuminate\Support\Facades\Artisan::call("db:seed", ["--force" => true]);
    echo "Seeding database (pertama kali)...\n";
} else {
    echo "Database sudah terisi, seed dilewati.\n";
}
'

# 5. Jalankan web server pada port dari platform ($PORT)
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
