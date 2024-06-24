FROM php:apache

# Install Composer
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/l>

# Install PHP extensions
RUN docker-php-ext-install mysqli

# Copy application source
COPY . /var/www/html

# Install dependencies
WORKDIR /var/www/html
RUN composer install
