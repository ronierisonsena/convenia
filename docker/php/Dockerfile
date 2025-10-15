FROM php:8.3-cli

# Install deps
RUN apt-get update && apt-get install -y \
    git unzip zip libpng-dev libjpeg-dev libonig-dev libxml2-dev libssl-dev \
    pkg-config libcurl4-openssl-dev curl supervisor gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g chokidar-cli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# deps required by laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

# Install and enable Swoole
RUN pecl install swoole && docker-php-ext-enable swoole

# inotify, used by octane file watch
RUN pecl install inotify && docker-php-ext-enable inotify

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/api

COPY ./api/ /var/www/html/api/

RUN cp .env.example .env

EXPOSE 8000

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000", "--watch"]
