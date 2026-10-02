FROM node:22-alpine AS assets

WORKDIR /app

RUN corepack enable

COPY package.json pnpm-lock.yaml ./

RUN pnpm install --frozen-lockfile

COPY . .

RUN pnpm run build


FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer dump-autoload --no-dev --optimize --no-scripts


FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libicu-dev \
        libpq-dev \
        libsqlite3-dev \
        libzip-dev \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        opcache \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        zip \
    && rm -rf /var/lib/apt/lists/*

# Apache
RUN a2enmod rewrite headers

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APP_DEBUG=false \
    APP_ENV=production \
    LOG_CHANNEL=stderr \
    PORT=10000

WORKDIR /var/www/html

# Application Laravel
COPY --from=vendor --chown=www-data:www-data /app/ ./

# Assets Vite
COPY --from=assets --chown=www-data:www-data /app/public/build/ ./public/build/

# CSS classiques dans public/css
COPY --from=vendor --chown=www-data:www-data /app/public/css/ ./public/css/

# Configuration Apache
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' \
    /etc/apache2/sites-available/000-default.conf \
    \
    && cat >> /etc/apache2/apache2.conf <<'EOF'

<Directory /var/www/html/public>
    AllowOverride All
    Options Indexes FollowSymLinks
    Require all granted
</Directory>
EOF

# Vérification des fichiers publics
RUN echo "===== PUBLIC =====" \
    && ls -lah /var/www/html/public \
    && echo "===== CSS =====" \
    && ls -lah /var/www/html/public/css \
    && echo "===== VERIFY CSS =====" \
    && test -f /var/www/html/public/css/niassy.css \
    && test -f /var/www/html/public/css/components.css \
    && test -f /var/www/html/public/css/notifications.css \
    && echo "===== CSS FILES OK ====="

# Laravel writable directories
RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && php artisan package:discover --ansi --no-interaction \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 10000

CMD sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf \
    && sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" \
        /etc/apache2/sites-available/000-default.conf \
    && exec apache2-foreground