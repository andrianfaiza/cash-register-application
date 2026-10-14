# ---------- Stage 1: build aset frontend (Vite) ----------
FROM node:22-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- Stage 2: runtime PHP ----------
FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install pdo_pgsql pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Buang node_modules & build lama dari konteks, lalu pasang hasil build dari stage 1
RUN rm -rf node_modules public/build \
    && composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && mkdir -p public/build

COPY --from=assets /app/public/build ./public/build

RUN chmod -R 775 storage bootstrap/cache \
    && chmod +x docker/entrypoint.sh

EXPOSE 8080
CMD ["sh", "docker/entrypoint.sh"]
