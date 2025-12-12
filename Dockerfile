FROM serversideup/php:8.3-fpm-nginx

# Switch to root for installing extensions
USER root

# Install additional PHP extensions
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libexif-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    intl \
    bcmath \
    gd \
    exif \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (for caching)
COPY --chown=www-data:www-data composer.json composer.lock ./

# Install composer dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY --chown=www-data:www-data . .

# Generate autoloader
RUN composer dump-autoload --optimize

# Install npm dependencies and build assets
RUN npm ci && npm run build && rm -rf node_modules

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create entrypoint script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Generate APP_KEY if not set\n\
if [ -z "$APP_KEY" ]; then\n\
    php artisan key:generate --force\n\
fi\n\
\n\
# Run migrations\n\
php artisan migrate --force || true\n\
\n\
# Run seeders\n\
php artisan db:seed --force || true\n\
\n\
# Cache config\n\
php artisan config:cache || true\n\
php artisan route:cache || true\n\
php artisan view:cache || true\n\
\n\
# Create storage link\n\
php artisan storage:link || true\n\
\n\
# Start the server\n\
exec /init\n\
' > /entrypoint.sh && chmod +x /entrypoint.sh

# Switch back to www-data user
USER www-data

# Expose port
EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
