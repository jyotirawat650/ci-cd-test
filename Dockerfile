FROM php:8.2-apache

# Install system dependencies and PHP extensions required for MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copy application source code into the container web root
COPY src/ /var/www/html/

EXPOSE 80