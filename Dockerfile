# Stage 1: Build assets
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP Application with Laravel Octane/Swoole
FROM phpswoole/swoole:php8.3-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git curl \
    libpng libjpeg-turbo freetype \
    libzip icu oniguruma libxml2 \
    && docker-php-ext-enable swoole

# Install additional PHP extensions
RUN apk add --no-cache \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev icu-dev oniguruma-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip intl bcmath opcache exif pcntl \
    && apk del libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev icu-dev oniguruma-dev libxml2-dev

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Copy built assets from node stage
COPY --from=assets /app/public/build ./public/build

# Generate autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

# Start Laravel with PHP built-in server
CMD php artisan migrate --force; php artisan db:seed --force; php artisan serve --host=0.0.0.0 --port=8000
