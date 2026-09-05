FROM dunglas/frankenphp:php8.3

RUN install-php-extensions pdo_mysql

WORKDIR /app

COPY . /app
COPY Caddyfile /etc/caddy/Caddyfile

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
