FROM dunglas/frankenphp:php8.3

# Installation des extensions PHP nécessaires
RUN install-php-extensions \
    pdo \
    pdo_mysql

# Dossier de l'application
WORKDIR /app

# Copier le projet
COPY . /app

# Port utilisé par Railway
ENV SERVER_NAME=:${PORT}

# Démarrage de FrankenPHP
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
