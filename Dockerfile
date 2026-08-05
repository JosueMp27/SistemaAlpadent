# Stage 1: Build Frontend Assets
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json vite.config.js ./
RUN npm install
COPY resources ./resources
COPY public ./public
RUN npm run build

# Stage 2: Production PHP-FPM + Nginx Application
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    libzip-dev

RUN docker-php-ext-install pdo pdo_mysql mbstring bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache/data storage/logs \
    && chmod -R 777 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x docker/entrypoint.sh

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
