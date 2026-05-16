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
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Build Dependencies & Frontend
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs && \
    npm install && npm run build

# 7. The Pre-Boot Execution Command
# This clears cache, forces a fresh migration, implants the user, and then starts Nginx.
CMD php-fpm -D && \
    php artisan optimize:clear && \
    php artisan migrate:fresh --force && \
    php artisan tinker --execute="\\App\\Models\\User::updateOrCreate(['email' => 'admin@motoshop.com'], ['name' => 'Admin', 'password' => \\Illuminate\\Support\\Facades\\Hash::make('NuclearShop2026!')]);" && \
    php artisan filament:upgrade && \
    nginx -g 'daemon off;'

# 8. The Final Start Command
CMD php-fpm -D && \
    php artisan optimize:clear && \
    php artisan migrate --force && \
    php artisan filament:upgrade && \
    nginx -g 'daemon off;'
