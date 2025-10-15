FROM php:8.3-fpm

# Install system dependencies required by Laravel
RUN apt-get update \
  && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    pkg-config \
    zip \
  && docker-php-ext-install pdo_mysql intl zip bcmath \
  && docker-php-ext-enable opcache \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Ensure storage and bootstrap cache directories exist during initial bootstrap
RUN mkdir -p storage/bootstrap/cache bootstrap/cache \
  && chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
