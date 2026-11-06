FROM php:8.2-apache
RUN docker-php-ext-install mysqli \
 && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# The Flutter app calls http://<ip>:<port>/scripts/<file>.php
COPY Codigo/scripts/scripts/ /var/www/html/scripts/
