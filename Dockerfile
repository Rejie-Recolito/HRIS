# -----------------------------------------------------
# Stage 1: Build Laravel App with LibreOffice and SQLite
# -----------------------------------------------------
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# -----------------------------------------------------
# Install system dependencies and PHP extensions
# -----------------------------------------------------
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    vim \
    libreoffice \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip bcmath

# -----------------------------------------------------
# Copy Composer binary from official image
# -----------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -----------------------------------------------------
# Copy project files to container
# -----------------------------------------------------
COPY . .

# -----------------------------------------------------
# Ensure storage and cache directories exist and are writable
# -----------------------------------------------------
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# -----------------------------------------------------
# Create SQLite database file if using sqlite
# -----------------------------------------------------
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && chmod 666 /var/www/html/database/database.sqlite

# -----------------------------------------------------
# Install PHP dependencies (no dev)
# -----------------------------------------------------
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader

# -----------------------------------------------------
# Generate Laravel key (optional: safe to skip if you provide .env)
# -----------------------------------------------------
RUN php artisan key:generate --force || true

# -----------------------------------------------------
# Expose PHP-FPM port and run
# -----------------------------------------------------
EXPOSE 9000
CMD ["php-fpm"]
