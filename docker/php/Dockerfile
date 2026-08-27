FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip mysqli \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000

CMD ["sh", "-c", "mkdir -p /var/www/html/App/Logs && chown -R www-data:www-data /var/www/html/App/Logs && chmod -R 775 /var/www/html/App/Logs && php migrate.php && php seed.php && php-fpm"]