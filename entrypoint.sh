#!/bin/bash
# Entrypoint for Render All-in-One Container

echo ">>> Starting Bloom Africa Deployment Script..."

# Set up MariaDB directories
mkdir -p /var/run/mysqld /var/lib/mysql
chown -R mysql:mysql /var/run/mysqld /var/lib/mysql
chmod 777 /var/run/mysqld

# Initialize MariaDB data directory if empty
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo ">>> Initializing MariaDB data directory (First run)..."
    mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null
    chown -R mysql:mysql /var/lib/mysql
fi

# Start MariaDB in background using mysqld_safe (More reliable for Docker)
echo ">>> Starting MariaDB Server..."
/usr/bin/mysqld_safe --datadir='/var/lib/mysql' --nowatch &

# Wait for MariaDB to be ready
echo ">>> Waiting for MariaDB to become healthy..."
RETRIES=30
while ! mysqladmin ping >/dev/null 2>&1; do
    RETRIES=$((RETRIES - 1))
    if [ $RETRIES -le 0 ]; then
        echo ">>> ERROR: MariaDB failed to start within 30 seconds."
        exit 1
    fi
    echo ">>> MariaDB starting... ($RETRIES attempts remaining)"
    sleep 1
done

echo ">>> MariaDB is UP and running!"

# Setup user and database
DB_NAME=${BLOOM_DB_NAME:-bloom_africa}
DB_PASS=${BLOOM_DB_PASS:-bloom_root_pass}

echo ">>> Configuring Database: $DB_NAME"
# Attempt to set root password and create user for 127.0.0.1
mysql -u root -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`;"
mysql -u root -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('$DB_PASS');" || true
mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '$DB_PASS' WITH GRANT OPTION;" || true
mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' IDENTIFIED BY '$DB_PASS' WITH GRANT OPTION;" || true
mysql -u root -e "FLUSH PRIVILEGES;"

echo ">>> Database successfully initialized."

# Start Apache in foreground
echo ">>> Starting Apache Web Server..."
exec apache2-foreground
