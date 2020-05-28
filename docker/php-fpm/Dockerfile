FROM php:7.4-fpm-alpine
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pdo

RUN \
 apk upgrade \
 && apk add \
        php7-intl \
        icu-dev

RUN docker-php-ext-install intl
RUN docker-php-ext-configure intl --enable-intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
