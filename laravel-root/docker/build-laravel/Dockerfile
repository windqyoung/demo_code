FROM php:apache

COPY laravel-apache.conf /etc/apache2/sites-enabled/
COPY phpinfo.php /var/www/html/
RUN mkdir -p /var/www/laravel/public
COPY phpinfo.php /var/www/laravel/public

RUN docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql

RUN a2enmod rewrite && service apache2 restart


