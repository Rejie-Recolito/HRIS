# -----------------------------------------------------
# ✅ Laravel + PHP-FPM + Nginx + LibreOffice (Render-Ready)
# -----------------------------------------------------
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and extensions
RUN apt-get update && apt-get install -y \
    nginx \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    libreoffice \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

# Ensure directories exist and set correct permissions
RUN mkdir -p storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ✅ Create SQLite file if not exists
RUN touch database/database.sqlite && chmod 666 database/database.sqlite

# Install PHP dependencies (safe for root)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader


# Expose HTTP port
EXPOSE 80

# Start both Nginx and PHP-FPM together
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080
