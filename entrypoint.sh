#!/bin/bash
# Start MySQL service
service mysql start

# Wait for MySQL to be ready
until mysqladmin ping >/dev/null 2>&1; do
  echo "Waiting for MySQL to start..."
  sleep 2
done

# Initialize the database
echo "Initializing Database..."
mysql -e "CREATE DATABASE IF NOT EXISTS bloom_africa;"
mysql -e "CREATE USER IF NOT EXISTS 'root'@'localhost' IDENTIFIED BY 'bloom_root_pass';"
mysql -e "GRANT ALL PRIVILEGES ON bloom_africa.* TO 'root'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Start Apache in the foreground
echo "Starting Apache..."
apache2-foreground
