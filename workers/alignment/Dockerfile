# Stage 1 - install php dependencies
FROM composer:1.9 as build-php
WORKDIR /opt/workers/alignment
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-suggest --no-interaction --prefer-dist --optimize-autoloader
COPY . ./
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# Stage 3 - copy the artifact and start process
FROM enyopharma/alignment:7.2
COPY ./process.conf /etc/process.conf
COPY --from=build-php /opt/workers/alignment /opt/workers/alignment
