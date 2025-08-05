FROM php:8.2-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libpng-dev \
    libzip-dev \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    libavif-bin \
    wget \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd \
    exif \
    pdo_pgsql \
    pgsql

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

# Копирование конфигураций PHP
COPY ./conf.d /usr/local/etc/php/conf.d/

WORKDIR /var/www