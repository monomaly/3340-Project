#!/bin/bash


set -e

PROJECT_DIR=$(pwd)
DB_NAME="store"
DB_USER="root"
DB_PASS=""

# Detect OS
if [[ "$OSTYPE" == "darwin"* ]]; then
    OS="mac"
elif [[ -f /etc/debian_version ]]; then
    OS="debian"
elif [[ -f /etc/fedora-release ]] || [[ -f /etc/redhat-release ]]; then
    OS="fedora"
else
    echo "Unsupported OS. This script supports Ubuntu/Debian, Fedora/RHEL, and Mac."
    exit 1
fi

echo ">>> Detected OS: $OS"

# Install dependencies
if [[ "$OS" == "debian" ]]; then
    echo ">>> Installing Apache, PHP, MySQL..."
    sudo apt update -qq
    sudo apt install -y apache2 php php-mysql php-pdo mysql-server

elif [[ "$OS" == "fedora" ]]; then
    echo ">>> Installing Apache, PHP, MySQL via dnf..."
    sudo dnf install -y httpd php php-mysqlnd php-pdo mysql-server

elif [[ "$OS" == "mac" ]]; then
    if ! command -v brew &>/dev/null; then
        echo "Homebrew not found. Install it from https://brew.sh then re-run."
        exit 1
    fi
    echo ">>> Installing Apache, PHP, MySQL via Homebrew..."
    brew install php mysql
    brew services start mysql
fi

# Start services (Linux only, Mac uses brew services)
if [[ "$OS" == "debian" ]]; then
    sudo systemctl start apache2
    sudo systemctl start mysql
elif [[ "$OS" == "fedora" ]]; then
    sudo systemctl start httpd
    sudo systemctl start mysqld
fi

# Setup MySQL database 
echo ">>> Setting up database '$DB_NAME'..."

sudo mysql -u root <<MYSQL
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`;
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';
FLUSH PRIVILEGES;
MYSQL

# Only import schema if DB has no tables yet (preserves existing data)
TABLE_COUNT=$(sudo mysql -u root -sse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME';")
if [[ "$TABLE_COUNT" -eq 0 ]]; then
    if [[ -f "$PROJECT_DIR/sql/schema.sql" ]]; then
        echo ">>> Importing schema.sql..."
        sudo mysql -u root "$DB_NAME" < "$PROJECT_DIR/sql/schema.sql"
    else
        echo "WARNING: No sql/schema.sql found, skipping import."
    fi
else
    echo ">>> Database already has data, skipping import."
fi

# Copy project to web root
if [[ "$OS" == "debian" ]]; then
    WEB_ROOT="/var/www/html/store"
    echo ">>> Copying project to $WEB_ROOT..."
    sudo rm -rf "$WEB_ROOT"
    sudo cp -r "$PROJECT_DIR" "$WEB_ROOT"
    sudo chown -R www-data:www-data "$WEB_ROOT"
    sudo chmod -R 755 "$WEB_ROOT"
    sudo a2enmod rewrite
    sudo systemctl restart apache2

elif [[ "$OS" == "fedora" ]]; then
    WEB_ROOT="/var/www/html/store"
    echo ">>> Copying project to $WEB_ROOT..."
    sudo rm -rf "$WEB_ROOT"
    sudo cp -r "$PROJECT_DIR" "$WEB_ROOT"
    sudo chown -R apache:apache "$WEB_ROOT"
    sudo chmod -R 755 "$WEB_ROOT"
    sudo setsebool -P httpd_can_network_connect_db 1
    sudo systemctl restart httpd

elif [[ "$OS" == "mac" ]]; then
    WEB_ROOT="/opt/homebrew/var/www/store"
    echo ">>> Copying project to $WEB_ROOT..."
    rm -rf "$WEB_ROOT"
    cp -r "$PROJECT_DIR" "$WEB_ROOT"
fi

# Done
echo ""
echo "✅ All done! Visit: http://localhost/store"
echo "   To stop services:"
if [[ "$OS" == "debian" ]]; then
    echo "     sudo systemctl stop apache2 mysql"
elif [[ "$OS" == "fedora" ]]; then
    echo "     sudo systemctl stop httpd mysqld"
elif [[ "$OS" == "mac" ]]; then
    echo "     brew services stop mysql php"
fi