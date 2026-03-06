FROM php:7.4-apache

# Install MariaDB Server and PHP extensions
RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y mariadb-server && \
    docker-php-ext-install pdo pdo_mysql mysqli && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Config MySQL for low memory (Super Important for Render Free Tier)
RUN echo "[mysqld]\n\
    skip-name-resolve\n\
    innodb_buffer_pool_size = 32M\n\
    max_connections = 10\n\
    performance_schema = OFF\n" > /etc/mysql/mariadb.conf.d/low-memory.cnf

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Setup Startup Script
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set the working directory
WORKDIR /var/www/html
COPY . /var/www/html/

# Ensure proper permissions
RUN chown -R www-data:www-data /var/www/html/

# Expose port 80
EXPOSE 80

# Start everything
CMD ["/entrypoint.sh"]
