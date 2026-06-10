FROM php:8.3-fpm

# Installer Composer et les dépendances nécessaires
RUN apt-get update && apt-get install -y curl unzip git
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer