# Multi-stage Dockerfile for Laravel + Vite

FROM php:8.2-fpm-alpine AS vendor
WORKDIR /app
# Install build dependencies and PHP extensions required by some locked packages (eg mpdf -> ext-gd)
RUN apk add --no-cache --virtual .build-deps \
		freetype-dev libpng-dev libjpeg-turbo-dev icu-dev zlib-dev libzip-dev oniguruma-dev build-base autoconf \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install gd pdo_mysql zip intl mbstring \
	&& apk add --no-cache freetype libpng libjpeg-turbo zlib icu-libs libzip oniguruma && rm -rf /var/cache/apk/*

# Install composer so we can run composer install in this stage
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

## Copy application files so composer scripts (which call artisan) can run
# .dockerignore excludes vendor, node_modules, and other heavy files
COPY . ./

# Ensure Laravel directories exist and are writable so composer scripts (package:discover) can run
RUN mkdir -p storage bootstrap/cache \
	&& chmod -R 0777 storage bootstrap/cache || true

# Allow composer to run as root inside the container and run install (this executes artisan scripts)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader

FROM node:20 AS assets
WORKDIR /app
# copy package files first to leverage caching
COPY package.json package-lock.json* ./
# install dependencies (do not silence failures)
RUN npm ci --no-audit --no-fund
# copy only the files required for the Vite build to reduce context size
COPY vite.config.* ./
COPY resources resources
COPY public public
RUN npm run build

FROM php:8.2-fpm-alpine
# system deps and runtime libs
RUN apk add --no-cache bash git freetype libpng libjpeg-turbo icu-libs zlib libzip oniguruma shadow curl
# build deps for compiling extensions
RUN apk add --no-cache --virtual .build-deps build-base autoconf freetype-dev libpng-dev libjpeg-turbo-dev icu-dev zlib-dev libzip-dev oniguruma-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install gd pdo_mysql zip intl mbstring \
	&& apk del .build-deps || true

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
