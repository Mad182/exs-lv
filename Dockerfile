FROM php:8.5-fpm

# Install Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Nginx, system dependencies & libraries
RUN apt-get update && apt-get install -y \
    nginx \
    libmemcached-dev \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configure & install required PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo_mysql \
    && pecl install igbinary \
    && docker-php-ext-enable igbinary \
    && pecl install memcached \
    && docker-php-ext-enable memcached

# Copy PHP limits configuration
COPY dev-draza/custom-php.ini $PHP_INI_DIR/conf.d/custom-php.ini

# Copy Nginx configuration
COPY dev-draza/nginx-docker.conf /etc/nginx/sites-available/default
RUN rm -f /etc/nginx/sites-enabled/default \
    && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

WORKDIR /var/www/exs-lv

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
