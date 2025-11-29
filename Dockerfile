FROM dunglas/frankenphp:1-php8.4

WORKDIR /var/www

COPY ./php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

RUN apt-get update && apt-get install -y --no-install-recommends \
    gnupg \
    g++ \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev \
    libonig-dev \
    libxslt1-dev \
    acl \
    librabbitmq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    install-php-extensions \
        @composer \
        redis \
        amqp \
        pdo_mysql \
        zip \
        xsl \
        gd \
        intl \
        opcache \
        exif \
        mbstring

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=prod

COPY . /var/www

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/var

COPY ./Caddyfile /etc/frankenphp/Caddyfile

CMD [ "frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile" ]
