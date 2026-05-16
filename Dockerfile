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

# 2. Inject Node.js & NPM (The Nuclear Trick for Vite)
COPY --from=node:20 /usr/local /usr/local

# 3. Nginx Config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Project Files & Immediate Ownership Transfer
WORKDIR /var/www/html
COPY . .

# Set ownership to www-data immediately so nothing builds as root
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Build Backend Dependencies
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 7. Build Frontend Production Assets (Fixes the Manifest Missing Error)
RUN npm install && npm run build

# 8. The Final Start Command
CMD php-fpm -D && \
    php artisan optimize:clear && \
    nginx -g 'daemon off;'
