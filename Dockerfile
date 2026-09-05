FROM dunglas/frankenphp:php8.3

RUN install-php-extensions pdo_mysql

WORKDIR /app

COPY . /app

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
