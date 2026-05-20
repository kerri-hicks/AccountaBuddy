FROM php:8.4-cli
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql
WORKDIR /app
COPY . /app
EXPOSE 80
# Run the scheduler in the background every 60 seconds, and run the web server in the foreground
CMD (while true; do php /app/cron/run.php; sleep 60; done) & exec php -S 0.0.0.0:80 index.php
