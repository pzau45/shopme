FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    bash

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json .
RUN composer dump-autoload --no-dev --optimize || true

COPY . /var/www/html/
RUN composer dump-autoload --no-dev --optimize

RUN chown -R www-data:www-data /var/www/html/public/uploads

EXPOSE 9000
CMD ["php-fpm"]
