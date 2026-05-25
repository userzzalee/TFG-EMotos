FROM php:8.4-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    zip \
    nodejs \
    npm

RUN docker-php-ext-install zip pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install

RUN npm install

EXPOSE 8000
EXPOSE 5173

CMD php artisan serve --host=0.0.0.0 --port=8000