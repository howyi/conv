#!/usr/bin/env bash
vendor/bin/phpunit --coverage-clover=build/log/clover.xml
vendor/bin/phpstan analyse -l 7 src
vendor/bin/phpcs --standard=PSR12 src tests
vendor/bin/php-coveralls -v