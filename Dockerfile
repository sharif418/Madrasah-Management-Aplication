# Stage 1: Build assets
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP Application
FROM webdevops/php-nginx:8.3-alpine

# Set environment variables
ENV WEB_DOCUMENT_ROOT=/app/public
ENV PHP_DISMOD=ioncube

# Set working directory
WORKDIR /app

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader

# Copy application files
COPY . .

# Copy built assets from node builder
COPY --from=node-builder /app/public/build ./public/build

# Generate autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R application:application /app \
    && chmod -R 755 /app/storage \
    && chmod -R 755 /app/bootstrap/cache

# Create startup script
RUN echo '#!/bin/bash \n\
set -e \n\
\n\
# Generate APP_KEY if not set \n\
if [ -z "$APP_KEY" ]; then \n\
    php artisan key:generate --force \n\
fi \n\
\n\
# Run migrations \n\
php artisan migrate --force || true \n\
\n\
# Run seeders \n\
php artisan db:seed --force || true \n\
\n\
# Cache config \n\
php artisan config:cache || true \n\
php artisan route:cache || true \n\
php artisan view:cache || true \n\
\n\
# Create storage link \n\
php artisan storage:link || true \n\
\n\
# Continue with default entrypoint \n\
exec /entrypoint supervisord' > /opt/docker/provision/entrypoint.d/20-laravel.sh \
    && chmod +x /opt/docker/provision/entrypoint.d/20-laravel.sh

EXPOSE 80
