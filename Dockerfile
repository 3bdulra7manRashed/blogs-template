FROM unit:1.34.1-php8.3

RUN apt update && apt install -y \
    curl unzip git libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pcntl opcache pdo pdo_mysql intl zip gd exif ftp bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit_buffer_size=256M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize=64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/custom.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install Node.js for building assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt install -y nodejs

WORKDIR /var/www/html

# Create storage directories with correct permissions
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R unit:unit /var/www/html/storage bootstrap/cache \
    && chmod -R 775 /var/www/html/storage

# Copy dependency files first for better layer caching
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Install Composer dependencies without scripts (artisan doesn't exist yet)
# This layer is cached unless composer files change
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

# Install npm dependencies (this layer is cached unless package files change)
RUN npm ci --prefer-offline --no-audit

# Copy application code (this layer invalidates only when code changes)
COPY . .

# Run Composer scripts now that artisan exists (package discovery, etc.)
RUN composer dump-autoload --optimize --no-interaction

# Build frontend assets (uses cached npm dependencies if code-only changes)
RUN npm run build && rm -rf node_modules && npm cache clean --force

# Set permissions for copied files
RUN chown -R unit:unit storage bootstrap/cache . \
    && chmod -R 775 storage bootstrap/cache

# Copy unit.json configuration
COPY unit.json /docker-entrypoint.d/unit.json

EXPOSE 8000

CMD ["unitd", "--no-daemon"]

