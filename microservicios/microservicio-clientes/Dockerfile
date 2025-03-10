FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Instalar dependencias
RUN apk add --no-cache \
    build-base \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    mysql-client

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto
COPY . .

# Crear directorios y establecer permisos
RUN mkdir -p storage/framework/cache storage/framework/views storage/framework/sessions \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Instalar dependencias con ignoreación de requisitos de plataforma
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Exponer puerto
EXPOSE 8000

# Ejecutar el servidor
CMD php -S 0.0.0.0:8000 -t public