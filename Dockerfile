FROM php:8.4-cli
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql
EXPOSE 80
CMD ["sh", "start.sh"]
