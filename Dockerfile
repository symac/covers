FROM php:7.4.25-apache


RUN apt-get update && \
   apt-get install -y unzip git zlib1g-dev libpng-dev libjpeg-dev 
# wget libarchive-tools vim unzip imagemagick poppler-utils

RUN docker-php-ext-install pdo pdo_mysql

RUN docker-php-ext-configure gd --with-jpeg --with-freetype

# Only then install it
RUN docker-php-ext-install gd

EXPOSE 80
RUN a2enmod rewrite

# COPY php.ini /usr/local/etc/php/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"