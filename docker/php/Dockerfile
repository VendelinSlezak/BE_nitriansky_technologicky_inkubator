FROM php:8.2-apache-bookworm

# 1. Povolenie Apache modulu
RUN a2enmod rewrite

# 2. Inštalácia systémových závislostí v jednom kroku
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    pkg-config \
    libonig-dev \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Konfigurácia a inštalácia PHP rozšírení
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && MAKEFLAGS="-j1" docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    intl \
    gd \
    opcache

# 4. Skopírovanie Composera z oficiálneho obrazu
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 5. Nastavenie pracovného adresára
WORKDIR /var/www/html
