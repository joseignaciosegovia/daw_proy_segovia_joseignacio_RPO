FROM php:8.2-apache

# -----------------------------
# 1. Dependencias del sistema
# -----------------------------
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev libicu-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql intl \
    && apt-get clean

# -----------------------------
# 2. Apache mod_rewrite
# -----------------------------
RUN a2enmod rewrite

# -----------------------------
# 3. Instalar Xdebug (CLAVE)
# -----------------------------
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# -----------------------------
# 4. Configuración Xdebug estable
# -----------------------------
RUN echo "xdebug.mode=debug" > /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/xdebug.ini \
 && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini

# -----------------------------
# 5. Composer
# -----------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# -----------------------------
# 6. Directorio de trabajo
# -----------------------------
WORKDIR /var/www/html

# -----------------------------
# 7. Copiar el proyecto completo
# -----------------------------
COPY . .

# -----------------------------
# 8. Instalar dependencias
# -----------------------------
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

RUN echo 'PassEnv STRIPE_SECRET_KEY' >> /etc/apache2/apache2.conf \
 && echo 'PassEnv STRIPE_PUBLIC_KEY' >> /etc/apache2/apache2.conf

# -----------------------------
# 9. Permisos
# -----------------------------
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html