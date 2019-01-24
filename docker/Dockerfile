FROM php:7.1
RUN yes "" | pecl install xdebug && docker-php-ext-enable xdebug
RUN apt-get update
RUN apt-get install -y --no-install-recommends git
RUN docker-php-ext-install pdo_mysql