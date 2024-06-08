FROM php:8.3.7-apache
RUN apt-get update && apt-get install -y \
        libzip-dev \
        librabbitmq-dev \
        libssl-dev \
        zip \
  && docker-php-ext-install zip pdo pdo_mysql sockets \
  && pecl install amqp \
  && docker-php-ext-enable amqp
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY . .
RUN composer install
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public/"]