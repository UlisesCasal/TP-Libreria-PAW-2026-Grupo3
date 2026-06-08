FROM php:8.2-cli

RUN apt-get update && apt-get install -y libzip-dev libpq-dev unzip \
    && docker-php-ext-install zip pdo pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

COPY . .

EXPOSE 10000

CMD php -S 0.0.0.0:${PORT:-10000} -t public
