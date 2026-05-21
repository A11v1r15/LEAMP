FROM php:8.5-apache

RUN a2enmod rewrite

RUN echo '<Directory /var/www/html>\
    AllowOverride All\
</Directory>' \
>> /etc/apache2/apache2.conf

COPY . /var/www/html

EXPOSE 80