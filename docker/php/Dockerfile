FROM php:8.4-fpm

WORKDIR /var/www/backend

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    unzip && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    zip \
    pcntl \
    opcache

RUN pecl install redis && docker-php-ext-enable redis
RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN groupadd -g 1000 www && \
    useradd -u 1000 -ms /bin/bash -g www www && \
    chown -R www:www /var/www