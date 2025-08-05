# Используем официальный образ PHP с Apache
FROM php:8.2-apache

#COPY . /usr/src/myapp
#WORKDIR /usr/src/myapp

#ENV COMPOSER_CACHE_DIR=/tmp

# Копируем исходный код в контейнер
COPY . /var/www/html

# Устанавливаем Composer
RUN apt-get update && \
    apt-get install -y curl git zip && \
    rm -rf /var/lib/apt/lists/* && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pgsql pdo_pgsql

# Устанавливаем необходимые расширения PHP (если нужно)
#RUN docker-php-ext-install pgsql pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install --no-dev --optimize-autoloader

#RUN composer install --no-dev

# Открываем порт 80
# EXPOSE 80

# Запускаем Apache
CMD ["apache2-foreground"]