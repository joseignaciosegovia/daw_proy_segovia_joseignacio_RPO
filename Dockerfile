FROM php:8.2-apache

# ---------------------------------------------------
# 1. Dependencias del sistema (capa cacheable estable)
# ---------------------------------------------------
RUN apt-get update && apt-get install -y \
    ca-certificates curl git unzip zip \
    libzip-dev libicu-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql intl \
    && update-ca-certificates

# ---------------------------------------------------
# 2. Apache modules (capa estable)
# ---------------------------------------------------
RUN a2enmod rewrite

# ---------------------------------------------------
# 3. Composer (capa estable, no cambia casi nunca)
# ---------------------------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ---------------------------------------------------
# 4. Directorio de trabajo
# ---------------------------------------------------
WORKDIR /var/www/html

# ---------------------------------------------------
# 5. Copiar SOLO archivos de dependencias primero (cache clave)
# ---------------------------------------------------
COPY composer.json composer.lock ./

# ---------------------------------------------------
# 6. Instalar dependencias PHP (capa cacheable)
# ---------------------------------------------------
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

# ---------------------------------------------------
# 7. Copiar el resto del proyecto (código cambia más a menudo)
# ---------------------------------------------------
COPY . .

# ---------------------------------------------------
# 8. Permisos finales
# ---------------------------------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/imagenes