FROM php:7.4-apache

# --- System packages & PHP extensions Laravel 5.8 needs ---
RUN apt-get update && apt-get install -y \
        git unzip libzip-dev libonig-dev sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite mbstring zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache should serve Laravel's /public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

COPY composer.json ./
RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize \
    && chmod +x docker/entrypoint.sh

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["apache2-foreground"]
