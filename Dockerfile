FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
  && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy dependency files first for layer caching
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Copy application code
COPY . .

# Optimise autoloader with full codebase present
RUN composer dump-autoload --optimize

# Set ownership and permissions for www-data
RUN chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
