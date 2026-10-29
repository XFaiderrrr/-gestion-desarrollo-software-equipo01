FROM php:7.3-apache

ARG SERVICE_DIR

RUN printf '%s\n' \
    'deb http://archive.debian.org/debian bullseye main' \
    'deb http://archive.debian.org/debian-security bullseye-security main' \
    > /etc/apt/sources.list \
    && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99archive \
    && apt-get update \
    && apt-get install -y --no-install-recommends libcurl4-openssl-dev \
    && docker-php-ext-install pdo_mysql mysqli curl \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY ${SERVICE_DIR}/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80