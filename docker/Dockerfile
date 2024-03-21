FROM php:7.3
RUN yes "" | pecl install xdebug-3.1.6 && docker-php-ext-enable xdebug
RUN apt-get update
RUN apt-get install -y --no-install-recommends git
RUN docker-php-ext-install pdo_mysql