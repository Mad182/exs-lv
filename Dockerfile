FROM php:8.2-apache

# Install system dependencies & libraries
RUN apt-get update && apt-get install -y \
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
    && pecl install memcached \
    && docker-php-ext-enable memcached

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache DocumentRoot to point to /var/www/exs-lv/exs.lv
ENV APACHE_DOCUMENT_ROOT /var/www/exs-lv/exs.lv

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides and configure Directory permissions
RUN echo '<Directory /var/www/exs-lv/exs.lv/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/exs-override.conf \
    && a2enconf exs-override

WORKDIR /var/www/exs-lv
