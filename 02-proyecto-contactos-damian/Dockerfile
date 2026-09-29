FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip default-mysql-client \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# El proyecto vive en /var/www/html para que Apache lo sirva como document root
WORKDIR /var/www/html

COPY composer.json composer.lock* ./
RUN composer install --no-interaction --no-dev --optimize-autoloader

COPY . .

# Apache ya publica el puerto 80
EXPOSE 80
