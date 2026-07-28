FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
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
    intl \
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

# Copy application code (includes pre-built public/build assets)
COPY . .

# Optimise autoloader with full codebase present
RUN composer dump-autoload --optimize

# Set ownership and permissions for www-data
RUN chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

# Ensure .env is always readable/writable by the www-data worker,
# even if it was baked into the image with wrong ownership from a previous
# manual docker cp. This runs on every build, so it self-heals.
RUN if [ -f .env ]; then chown www-data:www-data .env && chmod 644 .env; fi

EXPOSE 9000
CMD ["php-fpm"]
