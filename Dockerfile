FROM php:8.2-fpm

# 1. Install System Dependencies & Extensions in one clean layer
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

# 2. Configure Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# 4. Copy Project & Set Permissions (The Nuclear Version)
WORKDIR /var/www/html
COPY . .
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www/html

# 5. Build dependencies
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 6. Final Wake-Up Command
CMD php-fpm -D && php artisan optimize:clear && nginx -g 'daemon off;'


