FROM php:8.1-apache
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
COPY . /var/www/html/
CMD bash -c "APACHE_PORT=${PORT:-80} && sed -i \"s/Listen 80/Listen $APACHE_PORT/\" /etc/apache2/ports.conf && sed -i \"s/:80/:$APACHE_PORT/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"
