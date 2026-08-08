FROM php:8.5-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/src

COPY apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html