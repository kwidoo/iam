# Use the official PHP 8.4 CLI image as a base
FROM php:8.3-fpm AS prebuild

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    nano \
    procps \
    unzip \
    supervisor \
    cron \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nginx \
    logrotate \
    curl \
    gnupg \
    ca-certificates \
    fonts-freefont-ttf \
    libsodium-dev \
    --no-install-recommends && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install zip pdo pdo_mysql intl calendar gd exif sodium \
    && docker-php-ext-enable zip pdo_mysql intl calendar gd exif sodium

# Install MongoDB extension
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Increase PHP memory limit and other PHP configurations
RUN { \
    echo "memory_limit=512M"; \
    echo "upload_max_filesize=100M"; \
    echo "post_max_size=120M"; \
    echo "max_execution_time=300"; \
    echo "max_input_time=300"; \
    } > /usr/local/etc/php/conf.d/custom.ini

# Copy nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf
RUN rm -rf /etc/nginx/sites-enabled/default

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add Composer's global bin directory to the system PATH
ENV PATH /root/.composer/vendor/bin:$PATH

# Copy existing application directory contents and environment file
ARG APP_ENV=stage
COPY . /var/www
COPY .env.${APP_ENV} .env

# Switch to the web stage to install Laravel-specific dependencies
FROM prebuild AS web

# Set Composer environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1
ARG TKN
RUN composer config --global --auth github-oauth.github.com "$TKN"

# Install application dependencies
RUN if [ "$APP_ENV" = "testing" ]; then \
    (composer install && php artisan test --env=testing) || exit 1; \
    elif [ "$APP_ENV" = "local" ]; then \
    composer install; \
    else \
    composer install --no-dev --optimize-autoloader; \
    fi

# Ensure permissions for Laravel storage and cache directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Create supervisord.conf
RUN { \
    echo "[supervisord]"; \
    echo "nodaemon=true"; \
    echo "logfile=/tmp/supervisord.log"; \
    echo "pidfile=/tmp/supervisord.pid"; \
    echo ""; \
    echo "[program:php-fpm]"; \
    echo "command=php-fpm"; \
    echo ""; \
    echo "[program:nginx]"; \
    echo "command=nginx -g 'daemon off;'"; \
    echo ""; \
    } > /etc/supervisor/conf.d/supervisord.conf

# Configure logrotate for Laravel and Nginx logs
RUN { \
    echo "/var/www/storage/logs/*.log {"; \
    echo "    daily"; \
    echo "    missingok"; \
    echo "    rotate 7"; \
    echo "    compress"; \
    echo "    delaycompress"; \
    echo "    notifempty"; \
    echo "    create 640 www-data www-data"; \
    echo "}"; \
    } > /etc/logrotate.d/nginx_laravel

# Set up cron job to run logrotate
RUN echo "0 0 * * * /usr/sbin/logrotate /etc/logrotate.d/nginx_laravel > /dev/null 2>&1" > /etc/cron.d/logrotate

# Expose port 80
EXPOSE 80

# Ensure cron and supervisord are running
CMD ["sh", "-c", "cron && supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
