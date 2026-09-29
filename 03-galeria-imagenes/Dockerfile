FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --optimize-autoloader

COPY . .

# Apache corre como www-data: la carpeta de subidas debe existir y ser escribible
RUN mkdir -p uploads && chown -R www-data:www-data uploads

EXPOSE 80
