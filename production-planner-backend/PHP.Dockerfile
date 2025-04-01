FROM php:fpm

# Install system packages needed for composer and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Add Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer