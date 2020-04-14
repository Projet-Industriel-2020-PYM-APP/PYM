FROM node:latest AS builder
ENV NPM_CONFIG_LOGLEVEL info
WORKDIR /var/www
COPY package.json yarn.lock /var/www/
RUN yarn install
COPY . /var/www/
RUN yarn build

FROM php:fpm-alpine
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add git
RUN docker-php-ext-install pdo_mysql
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY --from=builder /var/www/ .
RUN chmod -R 777 /var/www/public/uploads
RUN composer install --no-dev --no-interaction --optimize-autoloader
CMD wait-for-it database:3306 -- bin/console doctrine:migrations:migrate ; php-fpm

EXPOSE 9000