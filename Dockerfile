FROM php:7.4-apache

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite (if needed for pretty URLs)
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy project files to the container
COPY . /var/www/html/

# Ensure proper permissions
RUN chown -R www-data:www-data /var/www/html/

# Expose port 80
EXPOSE 80
