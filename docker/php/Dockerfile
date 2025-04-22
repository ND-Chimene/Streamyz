FROM php:8.3-fpm-alpine

# Install dependencies
RUN apk --no-cache add curl git wget bash dpkg acl shadow

# Installer PHP extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions opcache iconv soap zip intl fileinfo \
    && install-php-extensions pdo redis mysqli pdo_mysql gd

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Créer le répertoire de travail
WORKDIR /var/www

# Préparer Symfony (cache/log) — utile pour éviter les erreurs 500
RUN mkdir -p var/cache/dev var/log && chown -R www-data:www-data var
