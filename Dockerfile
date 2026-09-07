FROM dunglas/frankenphp:php8.3

RUN install-php-extensions pdo_mysql

WORKDIR /app

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier uniquement les fichiers Composer
COPY composer.json composer.lock* ./

# Installer les dépendances
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY . /app
COPY Caddyfile /etc/caddy/Caddyfile

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
