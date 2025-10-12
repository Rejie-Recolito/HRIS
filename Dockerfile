# Stage 1: Build dependencies
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader

# Stage 2: Final runtime image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl libreoffice \
    && docker-php-ext-install pdo pdo_mysql zip bcmath

WORKDIR /var/www/html

# Copy the application files
COPY . .

# Copy vendor files from build stage
COPY --from=vendor /app/vendor ./vendor

# Expose port for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
