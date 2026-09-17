FROM php:8.1-cli
RUN docker-php-ext-install pdo pdo_mysql
COPY . /app
WORKDIR /app
CMD php -S 0.0.0.0:$PORT
