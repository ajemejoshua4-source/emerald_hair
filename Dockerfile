FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --ignore-platform-reqs

FROM php:8.4-fpm-bookworm
RUN apt-get update && apt-get install -y nginx supervisor libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo_pgsql mbstring bcmath opcache \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN chmod +x docker/entrypoint.sh \
 && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache public/uploads/products \
 && chown -R www-data:www-data storage bootstrap/cache public/uploads \
 && chmod -R 775 storage bootstrap/cache public/uploads
EXPOSE 10000
ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["/usr/bin/supervisord","-n","-c","/etc/supervisor/conf.d/supervisord.conf"]
