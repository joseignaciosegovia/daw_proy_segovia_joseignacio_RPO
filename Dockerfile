FROM php:8.2-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    git \
    libzip-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql

# Instalar xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configurar xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=192.168.18.6" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    git \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql intl

# Activar mod_rewrite de Apache
RUN a2enmod rewrite

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia el composer.json antes para aprovechar cache
COPY composer.json /var/www/html/

# Instala dependencias PHP
WORKDIR /var/www/html
RUN composer install

# Copiar código fuente
COPY . /var/www/html
WORKDIR /var/www/html

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
