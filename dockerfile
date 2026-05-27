# Use official PHP 8.2 FPM image
FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip \
    curl \
    git \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/lists/*

# Install Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy all project files to container
COPY . .

# Create persistent SQLite database directory structure
RUN mkdir -p /var/database && touch /var/database/database.sqlite

# Install PHP dependencies (for production)
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets only if a package.json exists
RUN if [ -f package.json ]; then npm install && npm run build; fi

# Set correct storage ownership and permissions for Laravel (www-data user)
RUN chown -R www-data:www-data /var/www /var/database \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose the web application port
EXPOSE 8000

# Run optimization pipelines, execute structural migrations, and boot up
CMD php artisan config:cache && \
    php artisan storage:link --force && \
    exec php artisan serve --host=0.0.0.0 --port=8000