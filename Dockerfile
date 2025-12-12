FROM serversideup/php:8.3-fpm-nginx

# Install additional extensions
RUN install-php-extensions \
    intl \
    bcmath \
    gd \
    exif \
    pcntl

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (for caching)
COPY composer.json composer.lock ./

# Install composer dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize

# Install npm dependencies and build assets
RUN npm ci && npm run build && rm -rf node_modules

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

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
php artisan migrate --force\n\
\n\
# Run seeders\n\
php artisan db:seed --force || true\n\
\n\
# Clear and cache config\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Create storage link\n\
php artisan storage:link || true\n\
\n\
# Start the server\n\
exec /init\n\
' > /entrypoint.sh && chmod +x /entrypoint.sh

# Expose port
EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
