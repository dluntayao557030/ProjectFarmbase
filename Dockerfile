FROM php:8.4-apache

# Install system packages and PHP extensions (including CA certificates)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    ca-certificates \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    && update-ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring xml gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Make Apache use Render's default web port 10000
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g' /etc/apache2/sites-available/000-default.conf

# Set Laravel public as document root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Allow .htaccess for Laravel
RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy full Laravel app
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install frontend dependencies and build Vite assets
RUN npm ci && npm run build

# Set permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache public/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache public/uploads

EXPOSE 10000

# Startup script: clear config cache and start Apache
RUN echo '#!/bin/bash\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]