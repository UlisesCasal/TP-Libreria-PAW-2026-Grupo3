FROM php:8.2-cli

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .

EXPOSE 10000

CMD php -S 0.0.0.0:${PORT:-10000} -t public
