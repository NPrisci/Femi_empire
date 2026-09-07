FROM dunglas/frankenphp:php8.3

WORKDIR /app

# Outils système nécessaires à Composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP
RUN install-php-extensions pdo_mysql zip

# Vérification : le build s'arrêtera ici si zip n'est pas disponible
RUN php -m | grep -i '^zip$'

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier les fichiers Composer
COPY composer.json composer.lock* ./

# Composer en tant que root dans le conteneur
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer les dépendances
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# Copier l'application
COPY . /app

# Configuration Caddy
COPY Caddyfile /etc/caddy/Caddyfile

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
