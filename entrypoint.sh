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

# Create setup script to run all at once
cat <<EOF > /tmp/db_setup.sql
-- Ensure database exists
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`;

-- Clear any old root users for these hosts if they exist (to avoid 'already exists' errors)
-- Then recreate and grant permissions
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '$DB_PASS' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' IDENTIFIED BY '$DB_PASS' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '$DB_PASS' WITH GRANT OPTION;

-- Ensure the 'root' user exists for 127.0.0.1 specifically
CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;

FLUSH PRIVILEGES;
EOF

echo ">>> Applying Database Permissions..."
# Run setup using root (try without password first, then with password)
mysql -u root < /tmp/db_setup.sql || mysql -u root -p"$DB_PASS" < /tmp/db_setup.sql
rm /tmp/db_setup.sql

echo ">>> Database successfully initialized."

# Start Apache in foreground
echo ">>> Starting Apache Web Server..."
exec apache2-foreground
