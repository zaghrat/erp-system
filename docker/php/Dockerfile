FROM php:8.3-fpm-alpine
# Set working directory inside the container
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    icu-dev \
    libxml2-dev \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    gmp-dev \
    oniguruma-dev \
    mysql-client \
    libzip-dev \
    pkgconf \
    autoconf \
    gcc \
    g++ \
    make \
    linux-headers

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j2 \
        pdo_mysql \
        intl \
        xml \
        gd \
        gmp \
        mbstring \
        opcache \
        zip \
        dom

# Install Xdebug via PECL (for debugging)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
# Configure Xdebug for debugging (for development only)
# remote_host should be the IP of your host machine where your IDE is listening.
# In a Docker Compose setup, 'host.docker.internal' often works for Docker Desktop.
# Otherwise, you might need to find your host's IP address.
RUN { \
    echo "xdebug.mode=debug"; \
    echo "xdebug.start_with_request=yes"; \
    echo "xdebug.client_host=host.docker.internal"; \
    echo "xdebug.client_port=9003"; \
    echo "xdebug.discover_client_host=false"; \
    echo "xdebug.idekey=PHPSTORM"; \
} | tee /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer globally
# This allows you to run 'composer install' inside the container
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Copy your application files (your Symfony project) into the container
# This is usually done in docker-compose.yml via volume mounts
# but if you want to bake the code into the image (e.g., for production builds),
# you would uncomment and use this:
COPY ./app /var/www/html

# Set appropriate permissions for the Symfony cache and logs directories
# This ensures that the web server user (www-data by default in PHP-FPM) can write to these directories.
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/public
USER www-data
# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Command to run when the container starts
# This starts the PHP-FPM process
CMD ["php-fpm"]