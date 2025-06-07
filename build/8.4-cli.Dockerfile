# Use an official PHP runtime as a base image
FROM php:8.4-cli-alpine

# Installation des dépendances et extensions en une seule couche (plus rapide)
RUN apk update && apk add --no-cache \
    dcron \
    openssl \
    # Dépendances pour les extensions PHP (évite la recompilation)
    postgresql-dev \
    icu-dev \
    tidyhtml-dev \
    libzip-dev && \
    # Installing the docker php extensions installer
    curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    # Installation de toutes les extensions en une fois
    install-php-extensions \
        opcache \
        gettext \
        iconv \
        intl \
        tidy \
        zip \
        sockets \
        pgsql \
        mysqli \
        pdo_mysql \
        pdo_pgsql \
        @composer && \
    # Configuration d'OPcache
    echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.revalidate_freq=60" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    # Nettoyage du cache APK
    rm -rf /var/cache/apk/*

EXPOSE 8000
COPY php.ini /usr/local/etc/php/

CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/html/public"]