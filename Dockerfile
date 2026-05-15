FROM php:8.2-fpm

# 1. Install System Dependencies (Added ICU and Magick libraries)
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
    && docker-php-ext-enable imagick

# 2. Configure Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copy Project
WORKDIR /var/www/html
COPY . .

# 5. Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Build with "Ignore Platform Req" (This stops the Exit Code 2)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 7. Start Script
CMD service nginx start && php-fpm
