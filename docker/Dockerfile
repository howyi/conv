FROM php:8.1
RUN yes "" | pecl install xdebug-3.1.6 && docker-php-ext-enable xdebug
RUN apt-get update
RUN apt-get install -y --no-install-recommends git unzip
RUN docker-php-ext-install pdo_mysql
# Run composer on the image PHP so dependencies resolve for the version under test.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
