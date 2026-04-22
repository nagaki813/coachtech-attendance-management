FROM php:8.1-fpm

COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    vim \
    libonig-dev \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring zip