FROM php:7-apache

COPY . /var/www/html

RUN a2enmod rewrite

