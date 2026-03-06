#!/bin/bash
set -e

echo "Starting deployment script..."

# Support MariaDB startup in Docker
mkdir -p /var/run/mysqld /var/lib/mysql
chown -R mysql:mysql /var/run/mysqld /var/lib/mysql
chmod 777 /var/run/mysqld

# Initialize MariaDB data directory if empty
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "Initializing MariaDB data directory..."
    mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null
    chown -R mysql:mysql /var/lib/mysql
fi

# Start MariaDB
echo "Starting MariaDB..."
service mysql start

# Wait for MariaDB to be ready
echo "Waiting for MariaDB to be ready..."
for i in {30..0}; do
    if mysqladmin ping >/dev/null 2>&1; then
        break
    fi
    echo "MariaDB is starting... ($i)"
    sleep 1
done

if ! mysqladmin ping >/dev/null 2>&1; then
    echo "MariaDB failed to start. Logs:"
    cat /var/log/mysql/error.log
    exit 1
fi

echo "MariaDB is UP!"

# DB Setup from environment variables
DB_NAME=${BLOOM_DB_NAME:-bloom_africa}
DB_USER=${BLOOM_DB_USER:-root}
DB_PASS=${BLOOM_DB_PASS:-bloom_root_pass}

echo "Setting up database: $DB_NAME"
mysql -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`;"

# Configure root user for local TCP access (127.0.0.1) as used in db.php
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'127.0.0.1' IDENTIFIED BY '$DB_PASS';"
mysql -e "GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'127.0.0.1';"
mysql -e "FLUSH PRIVILEGES;"

echo "Database successfully initialized."

# Start Apache
echo "Starting Apache..."
exec apache2-foreground
