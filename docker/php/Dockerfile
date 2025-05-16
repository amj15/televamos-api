FROM php:8.2-fpm

# Instalación de extensiones y herramientas necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install intl pdo pdo_pgsql opcache \
    && apt-get clean

# Instala Composer desde imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura variables de entorno
ARG APP_ENV=dev
ENV APP_ENV=${APP_ENV}

ARG APP_DEBUG=1
ENV APP_DEBUG=${APP_DEBUG}

WORKDIR /var/www/html
