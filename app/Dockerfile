# Stage 1 - install php dependencies
FROM composer:2.0 as build-php
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-req=php
COPY . ./
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# Stage 2 - build assets
FROM node:12 as build-assets
WORKDIR /var/www/html
COPY package.json package-lock.json ./
RUN npm ci && npm audit fix
COPY webpack.config.js tsconfig.json ./
COPY assets ./assets
RUN npm run prod

# Stage 3 - copy the artifacts and start php-fpm
FROM php:8.0-fpm
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install pdo_pgsql
COPY --from=build-php /var/www/html ./
COPY --from=build-assets /var/www/html/public/build ./public/build
