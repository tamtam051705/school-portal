FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files into the Apache web root
COPY . /var/www/html/

# Create the upload directory and grant write permissions to Apache
RUN mkdir -p /var/www/html/uploads/instructors \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 755 /var/www/html/uploads