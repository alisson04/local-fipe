FROM php:apache

# Instalação da extensão mysqli
RUN docker-php-ext-install mysqli