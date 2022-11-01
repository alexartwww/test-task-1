FROM php:8.1.11-cli-alpine
MAINTAINER "artem@aleksashkin.com" Artem Aleksashkin

RUN docker-php-ext-configure pcntl && docker-php-ext-install pcntl && docker-php-ext-enable pcntl
RUN docker-php-ext-configure pdo_mysql && docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql
RUN curl -sSL https://getcomposer.org/composer.phar -o /usr/local/bin/composer && chmod a+x /usr/local/bin/composer
