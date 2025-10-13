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

# Ensure Laravel storage subdirectories exist and are writable so composer scripts (package:discover) can run
RUN mkdir -p \
		storage/framework/cache/data \
		storage/framework/sessions \
		storage/framework/views \
		storage/logs \
		bootstrap/cache \
		database \
	&& chmod -R 0777 storage bootstrap/cache database || true

# Create sqlite database file so artisan (run by composer scripts) can access it during build
RUN touch database/database.sqlite \
	&& chmod 666 database/database.sqlite || true

# Allow composer to run as root inside the container and run install (this executes artisan scripts)
ENV APP_KEY=base64:temporarykey000000000000000000000000000=
# Provide DB env vars for the install step so artisan uses the sqlite file we created
RUN DB_CONNECTION=sqlite DB_DATABASE=/app/database/database.sqlite COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader
ENV APP_KEY=

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

FROM php:8.2-fpm
# Use Debian-based image so we can install LibreOffice (alpine LibreOffice is not practical)
RUN apt-get update && apt-get install -y --no-install-recommends \
		ca-certificates curl gnupg2 git unzip procps lsb-release \
		# LibreOffice and helpers for docx -> pdf
		libreoffice-core libreoffice-writer libreoffice-common libreoffice-java-common \
		poppler-utils ghostscript fonts-dejavu-core fonts-liberation \
		# runtime libs for php extensions
		libpng-dev libjpeg-dev libfreetype-dev libzip-dev zlib1g-dev libicu-dev \
	&& rm -rf /var/lib/apt/lists/*

# build deps for compiling php extensions
RUN apt-get update && apt-get install -y --no-install-recommends build-essential autoconf pkg-config \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install gd pdo_mysql zip intl mbstring \
	&& apt-get purge -y --auto-remove build-essential autoconf pkg-config \
	&& rm -rf /var/lib/apt/lists/*

# Install composer binary from vendor stage (was installed to /usr/local/bin)
COPY --from=vendor /usr/local/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html
# copy vendor and built assets from previous stages
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
# copy remaining app files from context (keeps same layout as during build)
COPY . .

# ensure permissions and runtime writable dirs
RUN chown -R www-data:www-data /var/www/html \
	&& chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000
CMD ["php-fpm"]
