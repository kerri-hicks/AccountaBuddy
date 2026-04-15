FROM php:8.4-cli
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql
WORKDIR /app
COPY . /app
RUN php register-commands.php 725184804898865153
EXPOSE 80
CMD ["php", "-S", "0.0.0.0:80", "index.php"]
