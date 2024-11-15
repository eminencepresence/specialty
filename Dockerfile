# Start with the official PHP image
FROM php:8.1-apache

# Install dependencies for GD and FreeType support
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

# Configure GD with JPEG and FreeType support, then install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the application code to the container
COPY . /var/www/html/

# Set correct permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Set the correct permissions for the assets directory, especially the fonts
RUN chmod -R 755 /var/www/html/assets/fonts

# Expose the port for HTTP
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
