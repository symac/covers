FROM php:7.4.25-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && \
   apt-get install -y unzip git
# wget libarchive-tools vim unzip imagemagick poppler-utils

EXPOSE 80
RUN a2enmod rewrite

# COPY php.ini /usr/local/etc/php/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"