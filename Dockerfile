FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --ignore-platform-reqs

FROM php:8.4-fpm-bookworm
RUN apt-get update && apt-get install -y nginx supervisor libpq-dev libzip-dev unzip && docker-php-ext-install pdo_pgsql mbstring bcmath opcache && rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache
EXPOSE 10000
CMD ["/usr/bin/supervisord","-n","-c","/etc/supervisor/conf.d/supervisord.conf"]
