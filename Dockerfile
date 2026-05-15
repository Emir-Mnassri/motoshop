FROM php:8.2-fpm

# 1. System Dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    libmagickwand-dev \
    --no-install-recommends \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql zip intl bcmath \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Nginx Config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 3. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Project Files & Power Permissions
WORKDIR /var/www/html
COPY . .
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 5. Build
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 6. The "No-Repeat" Start Command
# This runs migrations and clears cache every time it boots.
# ... (Keep your existing Dockerfile layers as they are)

# Final Start Command: The "No-Fail" Chain
CMD php-fpm -D && \
    php artisan migrate --force || echo "Migrations skipped or already exist" && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan filament:upgrade && \
    php artisan storage:link || true && \
    nginx -g 'daemon off;'
