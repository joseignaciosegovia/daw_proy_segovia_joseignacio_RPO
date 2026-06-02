FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    unzip zip git libzip-dev libicu-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql intl

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

# Instala dependencias
RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/imagenes