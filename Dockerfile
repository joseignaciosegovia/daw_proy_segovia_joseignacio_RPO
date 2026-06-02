FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    git \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql intl

# Activar mod_rewrite de Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar composer.json y composer.lock primero para aprovechar cache
COPY composer.json composer.lock ./

# Instalar dependencias PHP (sin dev y con autoloader optimizado)
RUN composer install --no-dev --optimize-autoloader

# Copiar el resto del código
COPY . .

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html