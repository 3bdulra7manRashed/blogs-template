#!/bin/bash
set -e

echo "Starting Laravel application setup..."

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
until php -r "
try {
    \$pdo = new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306}', '${DB_USERNAME:-laravel}', '${DB_PASSWORD:-laravel}');
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$pdo->exec('CREATE DATABASE IF NOT EXISTS ${DB_DATABASE:-laravel}');
    echo 'MySQL is ready!\n';
    exit(0);
} catch(PDOException \$e) {
    exit(1);
}
"; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done

# Install Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache configuration
echo "Caching configuration..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed essential data (only if database is fresh)
echo "Seeding essential data..."
php artisan db:seed --class=DeletedUserSeeder --force || true
php artisan db:seed --class=RolesAndPermissionsSeeder --force || true

# Seed admin user if environment variables are set
if [ -n "$ADMIN_EMAIL" ] && [ -n "$ADMIN_PASSWORD" ] && [ -n "$ADMIN_NAME" ]; then
    echo "Creating admin user..."
    php artisan db:seed --class=AdminUserSeeder --force || true
fi

# Create storage link
echo "Creating storage symlink..."
php artisan storage:link || true

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Setup completed successfully!"

