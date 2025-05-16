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

COPY wait-for-it.sh /usr/local/bin/wait-for-it

CMD: ["wait-for-it", "db:5432", "--", "php-fpm"]

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . /var/www/html

ARG APP_ENV=dev
ENV APP_ENV=${APP_ENV}

ARG APP_DEBUG=1
ENV APP_DEBUG=${APP_DEBUG}

RUN if [ "$APP_ENV" = "prod" ]; then \
        composer install --no-dev --optimize-autoloader; \
    else \
        composer install; \
    fi
