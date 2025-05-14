FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install intl pdo pdo_pgsql opcache

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
