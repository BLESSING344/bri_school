FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    curl \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        opcache

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html

COPY .docker/nginx.conf /etc/nginx/nginx.conf
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY .docker/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 8080

COPY .docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
