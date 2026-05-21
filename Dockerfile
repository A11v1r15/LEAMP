FROM php:8.5-apache

RUN a2enmod rewrite

RUN printf '<Directory /var/www/html>\n\
	AllowOverride All\n\
</Directory>\n' >> /etc/apache2/apache2.conf

COPY . /var/www/html/

RUN mkdir -p /var/www/html/cache \
	&& chmod -R 777 /var/www/html/cache

CMD ["apache2-foreground"]