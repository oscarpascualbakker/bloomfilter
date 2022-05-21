FROM php:8.1-apache

RUN apt-get update -y && \
    apt-get upgrade -y && \
    apt-get install -y libmcrypt-dev git zip unzip libzip-dev openssl libicu-dev libonig-dev nano

RUN docker-php-ext-configure zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure mbstring

RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring bcmath

RUN pecl install redis-5.3.7 && \
    pecl install xdebug-3.1.4 && \
    docker-php-ext-enable redis xdebug

COPY .docker/xdebug/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY .docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY composer.json /var/www/html

WORKDIR /var/www/html

# Get Composer!
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN php /usr/bin/composer install

# In order to use pretty URLs we need mod_rewrite to be enabled
RUN a2enmod rewrite
RUN service apache2 restart