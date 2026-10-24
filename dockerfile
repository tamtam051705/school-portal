FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

# Copy project files into the Apache web root
COPY . /var/www/html/

# Create the upload directories and grant full write permissions to www-data
RUN mkdir -p /var/www/html/images/users \
    && chown -R www-data:www-data /var/www/html/images \
    && chmod -R 775 /var/www/html/images