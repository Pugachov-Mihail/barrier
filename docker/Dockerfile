FROM richarvey/nginx-php-fpm

ADD .. /var/www/barrier

RUN rm -Rf /etc/nginx/sites-enabled/*

COPY docker/conf/nginx/site.conf /etc/nginx/sites-available/site.conf

RUN ln -s /etc/nginx/sites-available/site.conf /etc/nginx/sites-enabled/site.conf

