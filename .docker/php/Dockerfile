FROM php:8.2-fpm

WORKDIR /usr/local/apache2/htdocs/

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y curl git zip unzip libpng-dev libonig-dev libxml2-dev

RUN docker-php-ext-install pdo_mysql mysqli
RUN docker-php-ext-configure gd
RUN docker-php-ext-install gd
RUN pecl install xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
