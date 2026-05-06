FROM php:8.3-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# PHP extensies: curl nodig voor aims_notify
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

# Set document root
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Copy site files
COPY public/ /var/www/html/

# Apache config: allow .htaccess overrides
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html|g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

EXPOSE 80
