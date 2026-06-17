FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        gettext-base \
        libicu-dev \
        libpq-dev \
        unzip \
        zip \
    && docker-php-ext-install \
        bcmath \
        intl \
        opcache \
        pdo_pgsql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --no-scripts --no-autoloader

COPY . .
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/render-entrypoint.sh /usr/local/bin/render-entrypoint

RUN composer dump-autoload --no-dev --classmap-authoritative \
    && php bin/console importmap:install --no-interaction \
    && php bin/console asset-map:compile --env=prod --no-debug \
    && chmod +x /usr/local/bin/render-entrypoint \
    && mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public/uploads

ENV APP_ENV=prod
ENV APP_DEBUG=0

ENTRYPOINT ["render-entrypoint"]
CMD ["apache2-foreground"]
