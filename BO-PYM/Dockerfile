FROM node:latest AS builder
ENV NPM_CONFIG_LOGLEVEL info
WORKDIR /var/www
ADD . /var/www
RUN yarn install
RUN yarn build

FROM php:fpm-alpine
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add git
RUN docker-php-ext-install pdo_mysql
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD composer install -o ; wait-for-it database:3306 -- bin/console doctrine:migrations:migrate ; php-fpm

EXPOSE 9000