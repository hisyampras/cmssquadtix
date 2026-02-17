# syntax=docker/dockerfile:1.7

ARG PHP_VERSION=8.2

FROM php:${PHP_VERSION}-fpm-bookworm AS php_base
WORKDIR /var/www

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        opcache \
        pdo_mysql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

FROM php_base AS composer_deps
COPY --from=composer:2.8 /usr/bin/composer /usr/local/bin/composer

# Install Composer dependencies first for better layer caching.
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist

# Copy app source and rebuild optimized autoloader.
COPY . .
RUN if [ -f app/Helpers.php ] && [ ! -f app/helpers.php ]; then \
      ln -s Helpers.php app/helpers.php; \
    fi \
    && composer dump-autoload --no-dev --optimize

FROM node:22-bookworm-slim AS frontend_build
WORKDIR /var/www

# Install Node dependencies first for better layer caching.
COPY package.json package-lock.json ./
RUN npm ci && npm install --no-save @rollup/rollup-linux-x64-gnu

# Copy only files required for Vite build.
COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

FROM php_base AS runtime
WORKDIR /var/www

COPY --chown=www-data:www-data . .
COPY --from=composer_deps --chown=www-data:www-data /var/www/vendor ./vendor
COPY --from=frontend_build --chown=www-data:www-data /var/www/public/build ./public/build

RUN if [ -f app/Helpers.php ] && [ ! -f app/helpers.php ]; then \
      ln -s Helpers.php app/helpers.php; \
    fi \
    && rm -f bootstrap/cache/*.php \
    && mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
