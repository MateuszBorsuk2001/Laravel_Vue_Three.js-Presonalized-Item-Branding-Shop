FROM php:8.2-fpm

# Argumenty
ARG user=laravel
ARG uid=1000

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor

# Czyszczenie cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalacja rozszerzeń PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalacja Redis
RUN pecl install redis && docker-php-ext-enable redis

# Pobranie najnowszego Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tworzenie użytkownika systemowego
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Instalacja Node.js i npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

# Ustawienie katalogu roboczego
WORKDIR /var/www

# Kopiowanie plików aplikacji
COPY . /var/www

# Ustawienie uprawnień
RUN chown -R $user:www-data /var/www
RUN chmod -R 775 /var/www/storage
RUN chmod -R 775 /var/www/bootstrap/cache

USER $user

# Instalacja zależności PHP
RUN composer install --no-dev --optimize-autoloader

# Instalacja zależności Node.js
RUN npm install
RUN npm run build

EXPOSE 9000

CMD ["php-fpm"]