FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor

RUN docker-php-ext-install pdo pdo_pgsql zip pcntl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apk update && apk add --no-cache \
    autoconf \
    make \

    mongo-c-driver \
    openssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb


WORKDIR /var/www
