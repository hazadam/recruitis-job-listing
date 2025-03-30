FROM php:8.4.5-fpm-bookworm
RUN useradd -u 1000 application
RUN pecl install opentelemetry && docker-php-ext-enable opentelemetry \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && pecl install redis && docker-php-ext-enable redis \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
