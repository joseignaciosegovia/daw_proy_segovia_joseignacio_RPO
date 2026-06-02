FROM php:8.2-apache

# -------------------------------
# 1. Dependencias del sistema
# -------------------------------
RUN apt-get update && apt-get install -y \
    ca-certificates curl git unzip zip \
    libzip-dev libicu-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql intl \
    && update-ca-certificates

# -------------------------------
# 2. Apache modules
# -------------------------------
RUN a2enmod rewrite

# -------------------------------
# 3. Composer
# -------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# -------------------------------
# 4. Directorio de trabajo
# -------------------------------
WORKDIR /var/www/html

# -------------------------------
# 5. Copiar solo dependencias (para cache)
# -------------------------------
COPY composer.json composer.lock ./

# -------------------------------
# 6. Instalar dependencias PHP
# -------------------------------
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

# -------------------------------
# 7. Copiar resto del proyecto (sin sobrescribir vendor)
# -------------------------------
COPY . ./

# -------------------------------
# 8. Ajustar permisos
# -------------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/imagenes