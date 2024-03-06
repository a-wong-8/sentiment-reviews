FROM php:8.1-apache
#RUN a2enmod rewrite
#RUN a2enmod headers
#COPY apache2.conf /etc/apache2
#COPY 000-default.conf /etc/apache2/sites-enabled
#COPY php.ini /usr/local/etc/php
RUN service apache2 restart
