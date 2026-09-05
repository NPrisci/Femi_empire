FROM dunglas/frankenphp:php8.3

RUN install-php-extensions pdo_mysql

RUN php -m | grep -E 'PDO|pdo_mysql'

WORKDIR /app

COPY . /app

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
