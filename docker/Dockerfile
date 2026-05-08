FROM php:8.2-fpm

# Set Environment Variables
ENV key=value

# Arguments
ARG USER=www-data
ARG UID=1000
ARG GID=1000

# Installing dependencies
RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
            curl \
            libmemcached-dev \
            libz-dev \
            libpq-dev \
            libjpeg-dev \
            libpng-dev \
            libfreetype6-dev \
            libssl-dev \
            libwebp-dev \
            libxpm-dev \
            libmcrypt-dev \
            libonig-dev \
            zip \
            unzip \
            git; \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN set -eux; \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath; \
    docker-php-ext-configure gd \
            --with-jpeg \
            --with-webp \
            --with-xpm \
            --with-freetype; \
    docker-php-ext-install gd; \
    pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Create system user
RUN groupmod -g $GID www-data && \
    usermod -u $UID -g $GID www-data

# Set working directory
WORKDIR /var/www/html

# Copy application files (если нужно)
# COPY ../laravel/ .

# Set permissions
RUN chown -R www-data:www-data /var/www

# Switch to user
USER www-data

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
