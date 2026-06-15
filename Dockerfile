FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpq-dev \
    librdkafka-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Kafka PHP extension
RUN pecl install rdkafka \
    && docker-php-ext-enable rdkafka
    
RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www