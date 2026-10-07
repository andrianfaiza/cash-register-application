FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Tambahkan --no-scripts agar tidak gagal membaca DB saat build
RUN composer install --no-dev --optimize-autoloader --no-scripts
RUN npm install && npm run build

EXPOSE 10000

# Bungkus CMD dengan sh -c agar perintah berurutan jalan dengan benar
CMD sh -c "php artisan package:discover --ansi && php artisan config:cache && php artisan route:cache && (php artisan storage:link || true) && php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=10000"