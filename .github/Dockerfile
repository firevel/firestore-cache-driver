# vim: set ft=dockerfile :
# Configuration file for Docker

# Configuration file for Docker
FROM php:cli

# Install native extensions
# 1) Install dependencies and dev dependencies
# 2) Install common extensions
# 4) Remove dev dependencies
# 5) Remove compile-time deps
# 6) Delete extracted source and caches
RUN apt update \
    && apt install -yqq \
        libcurl4 libcurl4-openssl-dev \
        libxml2 libxml2-dev \
        libzip4 libzip-dev \
        libonig5 libonig-dev \
    && docker-php-ext-install -j "$(nproc)" \
        bcmath \
        curl \
        dom \
        mbstring \
        zip \
    && apt purge --autoremove -yqq \
        libcurl4-openssl-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Install pecl extensions
# 1) Install dependencies and dev dependencies
# 2) Build gRPC
# 3) Remove pecl build dir
# 4) Remove exanded source code
RUN apt update \
    && apt install -yqq \
        zlib1g zlib1g-dev \
    && pecl install grpc pcov \
    && docker-php-ext-enable grpc pcov \
    && rm -rf /tmp/pecl \
    && docker-php-source delete \
    && apt purge --autoremove -yqq \
        zlib1g-dev \
    && apt clean \
    && rm -rf /var/lib/apt/lists/*

# Install Git
RUN apt update \
    && apt install -yqq git \
    && apt clean \
    && rm -rf /var/lib/apt/lists/*

# Install latest version of Composer
RUN curl -L -o composer-setup.php -sS https://getcomposer.org/installer \
    && test "$(php -r "echo hash_file('sha384', 'composer-setup.php');")" = "$(curl -qL -o- https://composer.github.io/installer.sig)" \
    && php composer-setup.php -- --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

# Change working directory, and make sure it exists
RUN mkdir /var/www/testing
WORKDIR /var/www/testing
