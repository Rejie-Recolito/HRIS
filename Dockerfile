# Multi-stage Dockerfile for Laravel + Vite

FROM composer:2.6 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader

FROM node:18-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci --silent --no-audit --no-fund || true
COPY . ./
RUN npm run build

FROM php:8.2-fpm-alpine
# system deps
RUN apk add --no-cache bash git icu-dev zlib-dev libzip-dev libpng-dev oniguruma-dev shadow curl
# php extensions
RUN docker-php-ext-install pdo_mysql zip intl

# Install composer (already available in vendor stage, but keep composer for artisan commands)
COPY --from=vendor /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
# copy application files
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY . .

# permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000
CMD ["php-fpm"]
