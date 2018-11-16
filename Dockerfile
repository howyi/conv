FROM php:7.1
RUN yes "" | pecl install xdebug && docker-php-ext-enable xdebug
RUN docker-php-ext-install pdo_mysql