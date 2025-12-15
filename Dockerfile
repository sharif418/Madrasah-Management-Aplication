# Stage 1: Build assets (cached unless package*.json changes)
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install --legacy-peer-deps
COPY . .
RUN npm run build

# Stage 2: Composer dependencies (cached unless composer.* changes)
FROM composer:latest AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# Stage 3: PHP Application
FROM php:8.3-cli-alpine

# Install only required system dependencies
RUN apk add --no-cache \
    libpng libjpeg-turbo freetype \
    libzip icu oniguruma libxml2 \
    mysql-client

# Install PHP extensions
RUN apk add --no-cache --virtual .build-deps \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev icu-dev oniguruma-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip intl bcmath opcache exif pcntl \
    && apk del .build-deps

# Configure PHP for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /app

# Copy composer dependencies
COPY --from=composer /app/vendor ./vendor

# Copy application files
COPY . .

# Copy built assets
COPY --from=assets /app/public/build ./public/build

# Generate optimized autoloader
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && rm /usr/bin/composer

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

# Create entrypoint script for production
RUN echo '#!/bin/sh' > /entrypoint.sh \
    && echo 'set -e' >> /entrypoint.sh \
    && echo 'php artisan config:cache' >> /entrypoint.sh \
    && echo 'php artisan route:cache' >> /entrypoint.sh \
    && echo 'php artisan view:cache' >> /entrypoint.sh \
    && echo 'php artisan icons:cache || true' >> /entrypoint.sh \
    && echo 'php artisan filament:cache-components || true' >> /entrypoint.sh \
    && echo 'php artisan migrate --force || true' >> /entrypoint.sh \
    && echo 'php artisan db:seed --force || true' >> /entrypoint.sh \
    && echo 'php artisan storage:link || true' >> /entrypoint.sh \
    && echo 'exec php artisan serve --host=0.0.0.0 --port=8000' >> /entrypoint.sh \
    && chmod +x /entrypoint.sh

EXPOSE 8000

CMD ["/entrypoint.sh"]
