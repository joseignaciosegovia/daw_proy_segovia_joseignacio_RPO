FROM php:8.2-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    unzip zip git libzip-dev libicu-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql intl

# Activar mod_rewrite
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar WORKDIR
WORKDIR /var/www/html

# Copiar composer.json y composer.lock primero (para cache)
COPY composer.json composer.lock ./

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Copiar el resto del código
COPY . /var/www/html

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chown -R www-data:www-data /var/www/html/imagenes \
    && chmod -R 775 /var/www/html/imagenes