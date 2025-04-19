FROM php:8.2-fpm

# Instalar extensiones necesarias para Symfony + MySQL
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    zip \
    && docker-php-ext-install pdo pdo_mysql

# (Opcional) Copiar Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo por defecto
WORKDIR /var/www/html
