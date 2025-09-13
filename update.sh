#!/bin/bash

# Z5 Invoicing Deployment Script
# This script automates the deployment process after pulling changes from git

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check if we're in the right directory
if [ ! -f "www/artisan" ]; then
    print_error "This script must be run from the project root directory (/var/www/html/z5-invoicing)"
    exit 1
fi

print_status "Starting Z5 Invoicing deployment..."

# Navigate to Laravel project directory
cd www

print_status "Changed to Laravel project directory"

# Check if composer is installed
if ! command_exists composer; then
    print_error "Composer is not installed. Please install Composer first."
    exit 1
fi

# Check if npm is installed
if ! command_exists npm; then
    print_error "npm is not installed. Please install Node.js and npm first."
    exit 1
fi

# 1. Install/Update PHP Dependencies
print_status "Installing/updating PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
print_success "PHP dependencies updated"

# 2. Install/Update Node.js Dependencies
print_status "Installing/updating Node.js dependencies..."
npm install --production
print_success "Node.js dependencies updated"

# 3. Build Frontend Assets
print_status "Building frontend assets..."
npm run build
print_success "Frontend assets built"

# 4. Check if .env file exists
if [ ! -f ".env" ]; then
    print_warning ".env file not found. Please create it from .env.example"
    print_warning "You may need to configure your database and other settings"
fi

# 5. Run Database Migrations
print_status "Running database migrations..."
php artisan migrate --force
print_success "Database migrations completed"

# 6. Clear and Cache Application
print_status "Clearing and caching application..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
print_success "Application cached and optimized"

# 7. Set Proper Permissions
print_status "Setting proper permissions..."
if command_exists sudo; then
    sudo chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || print_warning "Could not change ownership (may need sudo privileges)"
    sudo chmod -R 775 storage bootstrap/cache 2>/dev/null || print_warning "Could not change permissions (may need sudo privileges)"
else
    chmod -R 775 storage bootstrap/cache 2>/dev/null || print_warning "Could not change permissions"
fi
print_success "Permissions set"

# 8. Restart Web Server (optional)
print_status "Checking web server status..."
if command_exists systemctl; then
    if systemctl is-active --quiet apache2; then
        print_status "Restarting Apache..."
        sudo systemctl restart apache2 2>/dev/null || print_warning "Could not restart Apache (may need sudo privileges)"
        print_success "Apache restarted"
    elif systemctl is-active --quiet nginx; then
        print_status "Restarting Nginx..."
        sudo systemctl restart nginx 2>/dev/null || print_warning "Could not restart Nginx (may need sudo privileges)"
        print_success "Nginx restarted"
    else
        print_warning "No active web server found (Apache/Nginx)"
    fi
else
    print_warning "systemctl not available, skipping web server restart"
fi

# 9. Optional: Run seeders if needed
read -p "Do you want to run database seeders? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Running database seeders..."
    php artisan db:seed --force
    print_success "Database seeders completed"
fi

# 10. Optional: Restart queues if using them
if [ -f "config/queue.php" ]; then
    read -p "Do you want to restart queues? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_status "Restarting queues..."
        php artisan queue:restart
        print_success "Queues restarted"
    fi
fi

# Final status check
print_status "Performing final checks..."

# Check if Laravel is working
if php artisan --version >/dev/null 2>&1; then
    print_success "Laravel application is working correctly"
else
    print_error "Laravel application may have issues"
fi

# Check storage permissions
if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
    print_success "Storage and cache directories are writable"
else
    print_warning "Storage or cache directories may not be writable"
fi

print_success "Deployment completed successfully!"
print_status "Your Z5 Invoicing application has been updated and is ready to use."

# Display helpful information
echo
print_status "Useful commands for future reference:"
echo "  - View logs: tail -f storage/logs/laravel.log"
echo "  - Clear cache: php artisan cache:clear"
echo "  - Run migrations: php artisan migrate"
echo "  - Check status: php artisan --version"
echo