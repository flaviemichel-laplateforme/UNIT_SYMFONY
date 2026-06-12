FROM php:8.3-fpm

# Installer Composer et les dépendances nécessaires
RUN apt-get update && apt-get install -y curl unzip git

# Installer les extensions PHP nécessaires pour la base de données
RUN docker-php-ext-install pdo pdo_mysql

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer