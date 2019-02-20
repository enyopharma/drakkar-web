FROM php:7.1-fpm
MAINTAINER Pierre Mallinjoud <pm@enyopharma.com>
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install pdo_pgsql
VOLUME ["/app"]
WORKDIR /app
CMD ["php-fpm"]
