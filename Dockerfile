# Start with the official PHP image
FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Copy the application code to the container
COPY . /var/www/html/

# Set the correct permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose the port for HTTP
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
