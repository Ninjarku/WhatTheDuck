FROM php:apache

RUN apt-get update && \
    apt-get install -y zip unzip git && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    docker-php-source extract && \
    docker-php-ext-install mysqli

WORKDIR /var/www/html

COPY . .

RUN composer install

EXPOSE 80
