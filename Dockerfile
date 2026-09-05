FROM dunglas/frankenphp:php8.3

RUN install-php-extensions pdo_mysql

WORKDIR /app

COPY . /app

RUN echo "===== PORT =====" && echo "$PORT" && \
    echo "===== CADDYFILE =====" && cat /etc/caddy/Caddyfile

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
