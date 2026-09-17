FROM php:8.1-apache
RUN docker-php-ext-install pdo pdo_mysql
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
COPY . /var/www/html/
EXPOSE ${PORT}
