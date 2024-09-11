FROM php:8.3-fpm-alpine

# installation des extensions supplémentaire
RUN apk add --no-cache icu-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-enable intl

ARG UNAME=www-data
ARG UGROUP=www-data
ARG UID=501
ARG GID=204

RUN deluser www-data \
    && addgroup -g $GID www-data \
    && adduser -u $UID -D -S -G www-data www-data

# installation de composer dans le container
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# déclaration du repertoire de travail
WORKDIR /var/www/html

