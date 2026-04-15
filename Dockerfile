FROM php:8.4-cli
RUN docker-php-ext-install pdo pdo_pgsql
EXPOSE 80
CMD ["sh", "start.sh"]
